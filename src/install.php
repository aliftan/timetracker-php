<?php
// Database initialization script
class DatabaseInstaller {
    private $db;

    public function __construct() {
        $config = require_once __DIR__ . '/../config/database.php';
        
        // Create database directory if it doesn't exist
        $dbDir = dirname($config['path']);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0777, true);
        }
        
        $this->db = Database::getInstance();
        $this->createTables();
    }

    private function createTables() {
        $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
        $statements = explode(';', $schema);
        
        foreach($statements as $statement) {
            if (trim($statement) !== '') {
                $this->db->query($statement);
            }
        }
        
        echo "Database tables created successfully!\n";
    }
}