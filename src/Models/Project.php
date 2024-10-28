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

    public function getUserProjects($userId, $limit = null)
    {
        $sql = 'SELECT p.*, 
                       COUNT(t.id) as task_count,
                       SUM(CASE WHEN t.status = "completed" THEN 1 ELSE 0 END) as completed_tasks
                FROM projects p
                LEFT JOIN tasks t ON p.id = t.project_id
                WHERE p.user_id = :user_id
                GROUP BY p.id
                ORDER BY p.created_at DESC';

        if ($limit) {
            $sql .= ' LIMIT ' . (int)$limit;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $projects = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $projects[] = $row;
        }
        return $projects;
    }

    public function getProjectStats($userId) {
        $sql = "
            SELECT 
                COUNT(*) as total_projects,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_projects
            FROM projects
            WHERE user_id = :user_id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $stats = $result->fetchArray(SQLITE3_ASSOC);
        
        return [
            'total_projects' => $stats['total_projects'] ?? 0,
            'active_projects' => $stats['active_projects'] ?? 0
        ];
    }
}
