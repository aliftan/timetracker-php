<?php
class Task
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        // Convert all inputs to minutes
        $estimatedTime = $data['time_unit'] === 'hours'
            ? floatval($data['estimated_time']) * 60  // Convert hours to minutes
            : floatval($data['estimated_time']);      // Already in minutes

        $stmt = $this->db->prepare('
            INSERT INTO tasks (project_id, name, description, estimated_time, status) 
            VALUES (:project_id, :name, :description, :estimated_time, :status)
        ');

        $stmt->bindValue(':project_id', $data['project_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':name', $data['name'], SQLITE3_TEXT);
        $stmt->bindValue(':description', $data['description'], SQLITE3_TEXT);
        $stmt->bindValue(':estimated_time', $estimatedTime, SQLITE3_INTEGER);  // Store as minutes
        $stmt->bindValue(':status', $data['status'], SQLITE3_TEXT);

        return $stmt->execute();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare('
            SELECT t.*, 
                COALESCE(SUM((strftime("%s", tm.end_time) - strftime("%s", tm.start_time))), 0) as tracked_seconds,
                t.estimated_time
            FROM tasks t
            LEFT JOIN timers tm ON t.id = tm.task_id AND tm.end_time IS NOT NULL
            WHERE t.id = :id
            GROUP BY t.id
        ');

        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function update($id, $data)
    {
        // Convert all inputs to minutes
        $estimatedTime = $data['time_unit'] === 'hours'
            ? floatval($data['estimated_time']) * 60  // Convert hours to minutes
            : floatval($data['estimated_time']);      // Already in minutes

        $stmt = $this->db->prepare('
            UPDATE tasks 
            SET name = :name, 
                description = :description,
                estimated_time = :estimated_time,
                status = :status
            WHERE id = :id
        ');

        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':name', $data['name'], SQLITE3_TEXT);
        $stmt->bindValue(':description', $data['description'], SQLITE3_TEXT);
        $stmt->bindValue(':estimated_time', $estimatedTime, SQLITE3_INTEGER);  // Store as minutes
        $stmt->bindValue(':status', $data['status'], SQLITE3_TEXT);

        return $stmt->execute();
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE tasks SET status = :status WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        return $stmt->execute();
    }


    public function delete($id)
    {
        try {
            // Start transaction
            $this->db->query('BEGIN TRANSACTION');

            // Delete associated timers first
            $stmt = $this->db->prepare('DELETE FROM timers WHERE task_id = :task_id');
            $stmt->bindValue(':task_id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            // Delete the task
            $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            // Commit transaction
            $this->db->query('COMMIT');
            return true;
        } catch (Exception $e) {
            // Rollback on error
            $this->db->query('ROLLBACK');
            return false;
        }
    }

    public function getProjectTasks($projectId)
    {
        $stmt = $this->db->prepare('
            SELECT t.*, 
                p.name as project_name,
                t.estimated_time,
                COALESCE(SUM((strftime("%s", tm.end_time) - strftime("%s", tm.start_time))), 0) as tracked_seconds
            FROM tasks t
            JOIN projects p ON t.project_id = p.id
            LEFT JOIN timers tm ON t.id = tm.task_id AND tm.end_time IS NOT NULL
            WHERE t.project_id = :project_id
            GROUP BY t.id
            ORDER BY t.created_at DESC
        ');

        $stmt->bindValue(':project_id', $projectId, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $tasks = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $tasks[] = $row;
        }
        return $tasks;
    }

    public function getTaskStats($userId)
    {
        $sql = "
        SELECT 
            COUNT(DISTINCT t.id) as total_tasks,
            COUNT(DISTINCT CASE WHEN t.status = 'completed' THEN t.id END) as completed_tasks,
            COUNT(DISTINCT CASE WHEN t.status = 'in_progress' THEN t.id END) as in_progress_tasks,
            COUNT(DISTINCT CASE WHEN t.status = 'pending' THEN t.id END) as pending_tasks
        FROM tasks t
        JOIN projects p ON t.project_id = p.id
        WHERE p.user_id = :user_id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $stats = $result->fetchArray(SQLITE3_ASSOC);

        return [
            'total_tasks' => (int)($stats['total_tasks'] ?? 0),
            'completed_tasks' => (int)($stats['completed_tasks'] ?? 0),
            'in_progress_tasks' => (int)($stats['in_progress_tasks'] ?? 0),
            'pending_tasks' => (int)($stats['pending_tasks'] ?? 0)
        ];
    }

    public function getUserTasks($userId, $limit = null)
    {
        $sql = "
        SELECT 
            t.*, 
            p.name as project_name,
            ROUND(t.estimated_time / 60.0, 1) as estimated_hours,
            ROUND(COALESCE(
                SUM(
                    CASE 
                        WHEN tm.end_time IS NULL THEN 
                            (strftime('%s', 'now', 'localtime') - strftime('%s', tm.start_time)) / 3600.0
                        ELSE 
                            (strftime('%s', tm.end_time) - strftime('%s', tm.start_time)) / 3600.0
                    END
                ), 0
            ), 1) as tracked_hours
        FROM tasks t
        JOIN projects p ON t.project_id = p.id
        LEFT JOIN timers tm ON t.id = tm.task_id
        WHERE p.user_id = :user_id
        GROUP BY t.id
        ORDER BY t.created_at DESC
    " . ($limit ? " LIMIT " . (int)$limit : "");

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $tasks = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $tasks[] = $row;
        }
        return $tasks;
    }
}
