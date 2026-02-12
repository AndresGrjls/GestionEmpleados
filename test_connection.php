<?php
// Test file to diagnose connection issues
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'message' => 'PHP is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => phpversion()
]);

// Test database connection
try {
    $host = 'localhost';
    $dbName = 'employee_management';
    $username = 'root';
    $password = '';
    
    $dsn = "mysql:host={$host};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    echo "\nDatabase server connection: SUCCESS";
    
    // Try to connect to specific database
    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    echo "\nDatabase '{$dbName}' connection: SUCCESS";
    
} catch (PDOException $e) {
    echo "\nDatabase connection ERROR: " . $e->getMessage();
}
?>