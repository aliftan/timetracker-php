<?php
class Timer
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function startTimer($taskId, $userId)
    {
        // First stop any active timers
        $this->stopActiveTimers($userId);

        $stmt = $this->db->prepare('
            INSERT INTO timers (task_id, user_id, start_time) 
            VALUES (:task_id, :user_id, strftime("%Y-%m-%d %H:%M:%S", "now", "localtime"))
        ');

        $stmt->bindValue(':task_id', $taskId, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);

        return $stmt->execute();
    }

    public function stopTimer($timerId)
    {
        $stmt = $this->db->prepare('
            UPDATE timers 
            SET end_time = strftime("%Y-%m-%d %H:%M:%S", "now", "localtime")
            WHERE id = :timer_id AND end_time IS NULL
        ');

        $stmt->bindValue(':timer_id', $timerId, SQLITE3_INTEGER);
        return $stmt->execute();
    }

    private function stopActiveTimers($userId)
    {
        $stmt = $this->db->prepare('
            UPDATE timers 
            SET end_time = strftime("%Y-%m-%d %H:%M:%S", "now", "localtime")
            WHERE user_id = :user_id AND end_time IS NULL
        ');

        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        return $stmt->execute();
    }

    public function getActiveTimer($userId)
    {
        $stmt = $this->db->prepare('
            SELECT t.*, 
                   tk.name as task_name, 
                   p.name as project_name,
                   strftime("%s", "now") - strftime("%s", t.start_time) as duration_seconds
            FROM timers t
            JOIN tasks tk ON t.task_id = tk.id
            JOIN projects p ON tk.project_id = p.id
            WHERE t.user_id = :user_id AND t.end_time IS NULL
        ');

        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getTimerHistory($taskId)
    {
        $sql = "
            SELECT 
                id,
                start_time,
                end_time,
                CASE 
                    WHEN end_time IS NULL THEN 
                        strftime('%s', 'now') - strftime('%s', start_time)
                    ELSE 
                        strftime('%s', end_time) - strftime('%s', start_time)
                END as duration_seconds
            FROM timers
            WHERE task_id = :task_id
            ORDER BY start_time DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':task_id', $taskId, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $history = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $history[] = $row;
        }
        return $history;
    }

    public function getTrackedSeconds($taskId)
    {
        $sql = "
            SELECT COALESCE(
                SUM(
                    CASE 
                        WHEN end_time IS NULL THEN 
                            CAST((julianday('now', 'localtime') - julianday(start_time)) * 86400 as INTEGER)
                        ELSE 
                            CAST((julianday(end_time) - julianday(start_time)) * 86400 as INTEGER)
                    END
                ), 0
            ) as total_seconds
            FROM timers
            WHERE task_id = :task_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':task_id', $taskId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return max(0, $result->fetchArray(SQLITE3_ASSOC)['total_seconds']); // Ensure non-negative
    }

    public function getTimerStats($userId, $period = 'today')
    {
        $dateFilter = match ($period) {
            'today' => "date(start_time) = date('now')",
            'week' => "date(start_time) >= date('now', '-7 days')",
            'month' => "date(start_time) >= date('now', '-1 month')",
            default => "1=1"
        };

        $sql = "
            SELECT 
                COUNT(DISTINCT task_id) as tasks_tracked,
                COALESCE(
                    SUM(
                        CASE 
                            WHEN end_time IS NULL THEN 
                                strftime('%s', 'now') - strftime('%s', start_time)
                            ELSE 
                                strftime('%s', end_time) - strftime('%s', start_time)
                        END
                    ), 0
                ) as total_seconds
            FROM timers
            WHERE user_id = :user_id AND $dateFilter
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $stats = $result->fetchArray(SQLITE3_ASSOC);

        return [
            'total_seconds' => $stats['total_seconds'] ?? 0,
            'total_hours' => ($stats['total_seconds'] ?? 0) / 3600,
            'tasks_tracked' => $stats['tasks_tracked'] ?? 0
        ];
    }

    public function getTaskTimerSummary($taskId)
    {
        $sql = "
            SELECT 
                COUNT(*) as timer_count,
                MIN(start_time) as first_tracked,
                MAX(COALESCE(end_time, datetime('now'))) as last_tracked,
                COALESCE(
                    SUM(
                        CASE 
                            WHEN end_time IS NULL THEN 
                                strftime('%s', 'now') - strftime('%s', start_time)
                            ELSE 
                                strftime('%s', end_time) - strftime('%s', start_time)
                        END
                    ), 0
                ) as total_seconds
            FROM timers
            WHERE task_id = :task_id
            GROUP BY task_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':task_id', $taskId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function getRecentTimers($userId, $limit = 5)
    {
        $sql = "
            SELECT t.*, 
                   tk.name as task_name,
                   p.name as project_name,
                   CASE 
                       WHEN t.end_time IS NULL THEN 
                           strftime('%s', 'now') - strftime('%s', t.start_time)
                       ELSE 
                           strftime('%s', t.end_time) - strftime('%s', t.start_time)
                   END as duration_seconds
            FROM timers t
            JOIN tasks tk ON t.task_id = tk.id
            JOIN projects p ON tk.project_id = p.id
            WHERE t.user_id = :user_id
            ORDER BY t.start_time DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $timers = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $timers[] = $row;
        }
        return $timers;
    }

    public function getDailyTimerStats($userId, $days = 7)
    {
        $sql = "
            WITH RECURSIVE dates(date) AS (
                SELECT date('now', '-' || :days || ' days')
                UNION ALL
                SELECT date(date, '+1 day')
                FROM dates
                WHERE date < date('now')
            )
            SELECT 
                dates.date,
                COALESCE(
                    SUM(
                        CASE 
                            WHEN t.end_time IS NULL THEN 
                                strftime('%s', 'now') - strftime('%s', t.start_time)
                            ELSE 
                                strftime('%s', t.end_time) - strftime('%s', t.start_time)
                        END
                    ), 0
                ) as total_seconds,
                COUNT(DISTINCT t.task_id) as tasks_tracked
            FROM dates
            LEFT JOIN timers t ON date(t.start_time) = dates.date AND t.user_id = :user_id
            GROUP BY dates.date
            ORDER BY dates.date ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':days', $days, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $stats = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $stats[] = $row;
        }
        return $stats;
    }
}
