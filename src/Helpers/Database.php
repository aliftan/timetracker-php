<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $config = require_once __DIR__ . '/../../config/database.php';
        try {
            // Create database directory if it doesn't exist
            $dbDir = dirname($config['path']);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }
            
            $this->connection = new SQLite3($config['path']);
            $this->connection->enableExceptions(true);
            
            // Initialize database schema if needed
            $this->initializeSchema();
        } catch(Exception $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql) {
        return $this->connection->query($sql);
    }
    
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
    
    private function initializeSchema() {
        $schema = file_get_contents(__DIR__ . '/../../database/schema.sql');
        $statements = explode(';', $schema);
        
        foreach($statements as $statement) {
            if (trim($statement) !== '') {
                $this->query($statement);
            }
        }
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertRowID();
    }
}