<?php
class Project
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare('
            INSERT INTO projects (user_id, name, description) 
            VALUES (:user_id, :name, :description)
        ');

        $stmt->bindValue(':user_id', $data['user_id'], SQLITE3_INTEGER);
        $stmt->bindValue(':name', $data['name'], SQLITE3_TEXT);
        $stmt->bindValue(':description', $data['description'], SQLITE3_TEXT);

        return $stmt->execute();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM projects WHERE id = :id');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare('
            UPDATE projects 
            SET name = :name, 
                description = :description,
                is_active = :is_active
            WHERE id = :id
        ');

        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':name', $data['name'], SQLITE3_TEXT);
        $stmt->bindValue(':description', $data['description'], SQLITE3_TEXT);
        $stmt->bindValue(':is_active', $data['is_active'], SQLITE3_INTEGER);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $this->db->query('BEGIN TRANSACTION');

        try {
            // First delete associated timers
            $stmt = $this->db->prepare('
            DELETE FROM timers 
            WHERE task_id IN (SELECT id FROM tasks WHERE project_id = :project_id)
        ');
            $stmt->bindValue(':project_id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            // Then delete associated tasks
            $stmt = $this->db->prepare('DELETE FROM tasks WHERE project_id = :project_id');
            $stmt->bindValue(':project_id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            // Finally delete the project
            $stmt = $this->db->prepare('DELETE FROM projects WHERE id = :id');
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->execute();

            $this->db->query('COMMIT');
            return true;
        } catch (Exception $e) {
            $this->db->query('ROLLBACK');
            return false;
        }
    }

    public function getProjectStats($userId)
    {
        $sql = "
        SELECT 
            COUNT(DISTINCT id) as total_projects,
            COUNT(DISTINCT CASE WHEN is_active = 1 THEN id END) as active_projects
        FROM projects
        WHERE user_id = :user_id
    ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $stats = $result->fetchArray(SQLITE3_ASSOC);

        return [
            'total_projects' => (int)($stats['total_projects'] ?? 0),
            'active_projects' => (int)($stats['active_projects'] ?? 0)
        ];
    }

    public function getUserProjects($userId, $limit = null)
    {
        $sql = "
        SELECT 
            p.*, 
            COUNT(DISTINCT t.id) as task_count,
            COUNT(DISTINCT CASE WHEN t.status = 'completed' THEN t.id END) as completed_tasks
        FROM projects p
        LEFT JOIN tasks t ON p.id = t.project_id
        WHERE p.user_id = :user_id
        GROUP BY p.id
        ORDER BY p.created_at DESC
    " . ($limit ? " LIMIT " . (int)$limit : "");

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $projects = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $row['task_count'] = (int)($row['task_count'] ?? 0);
            $row['completed_tasks'] = (int)($row['completed_tasks'] ?? 0);
            $projects[] = $row;
        }
        return $projects;
    }
}
