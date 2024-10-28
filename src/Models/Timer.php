class Timer {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function startTimer($taskId, $userId) {
        $stmt = $this->db->prepare('
            INSERT INTO timers (task_id, user_id, start_time) 
            VALUES (:task_id, :user_id, datetime("now"))
        ');
        
        $stmt->bindValue(':task_id', $taskId, SQLITE3_INTEGER);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        
        return $stmt->execute();
    }
    
    public function stopTimer($timerId) {
        $stmt = $this->db->prepare('
            UPDATE timers 
            SET end_time = datetime("now") 
            WHERE id = :timer_id AND end_time IS NULL
        ');
        
        $stmt->bindValue(':timer_id', $timerId, SQLITE3_INTEGER);
        return $stmt->execute();
    }
    
    public function getActiveTimer($userId) {
        $stmt = $this->db->prepare('
            SELECT t.*, tk.name as task_name, p.name as project_name
            FROM timers t
            JOIN tasks tk ON t.task_id = tk.id
            JOIN projects p ON tk.project_id = p.id
            WHERE t.user_id = :user_id AND t.end_time IS NULL
        ');
        
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }
}