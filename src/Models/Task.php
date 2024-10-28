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

    public function getUserTasks($userId, $limit = null)
    {
        $sql = "
            SELECT t.*, 
                   p.name as project_name,
                   COALESCE(SUM((strftime('%s', tm.end_time) - strftime('%s', tm.start_time))/3600.0), 0) as tracked_hours
            FROM tasks t
            JOIN projects p ON t.project_id = p.id
            LEFT JOIN timers tm ON t.id = tm.task_id AND tm.end_time IS NOT NULL
            WHERE p.user_id = :user_id
            GROUP BY t.id
            ORDER BY t.created_at DESC
        ";

        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
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
                COUNT(*) as total_tasks,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_tasks,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_tasks
            FROM tasks t
            JOIN projects p ON t.project_id = p.id
            WHERE p.user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $stats = $result->fetchArray(SQLITE3_ASSOC);

        // Ensure all stats have at least 0 value
        return [
            'total_tasks' => $stats['total_tasks'] ?? 0,
            'completed_tasks' => $stats['completed_tasks'] ?? 0,
            'in_progress_tasks' => $stats['in_progress_tasks'] ?? 0,
            'pending_tasks' => $stats['pending_tasks'] ?? 0
        ];
    }

    public function delete($id)
    {
        // Start transaction
        $this->db->exec('BEGIN');

        try {
            // Delete associated timers first
            $stmt = $this->db->prepare('DELETE FROM timers WHERE task_id = :task_id');
            $stmt->bindValue(':task_id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            // Delete the task
            $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            // Commit transaction
            $this->db->exec('COMMIT');
            return true;
        } catch (Exception $e) {
            // Rollback on error
            $this->db->exec('ROLLBACK');
            return false;
        }
    }
}
