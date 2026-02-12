<?php
class Database {
    private $host = 'localhost';
    private $dbName = 'employee_management';
    private $username = 'root';
    private $password = '';
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            // First try to connect to MySQL server without specific database
            $dsn = "mysql:host={$this->host};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $tempConnection = new PDO($dsn, $this->username, $this->password, $options);
            
            // Create database if it doesn't exist
            $tempConnection->exec("CREATE DATABASE IF NOT EXISTS {$this->dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Now connect to the specific database
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function createTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS employees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            firstName VARCHAR(50) NOT NULL,
            lastName VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            position VARCHAR(100) NOT NULL,
            salary DECIMAL(10,2) NOT NULL,
            hireDate DATE NOT NULL,
            status ENUM('active', 'inactive', 'terminated') DEFAULT 'active',
            terminationDate DATE NULL,
            createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        try {
            $this->connection->exec($sql);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Table creation failed: " . $e->getMessage());
        }
    }
}
?>