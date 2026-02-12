<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'Employee.php';

try {
    $employee = new Employee();
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGetRequest($employee);
            break;
            
        case 'POST':
            handlePostRequest($employee);
            break;
            
        case 'PUT':
            handlePutRequest($employee);
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}

function handleGetRequest($employee) {
    if (isset($_GET['stats'])) {
        // Get employee statistics
        $result = $employee->getEmployeeStats();
        echo json_encode($result);
        
    } elseif (isset($_GET['id'])) {
        // Get specific employee by ID
        $result = $employee->getEmployeeById($_GET['id']);
        echo json_encode($result);
        
    } elseif (isset($_GET['search'])) {
        // Search employees
        $searchTerm = trim($_GET['search']);
        if (empty($searchTerm)) {
            echo json_encode([
                'success' => false,
                'message' => 'Search term is required'
            ]);
            return;
        }
        
        $result = $employee->searchEmployees($searchTerm);
        echo json_encode($result);
        
    } else {
        // Invalid GET request
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid GET request parameters'
        ]);
    }
}

function handlePostRequest($employee) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data'
        ]);
        return;
    }
    
    // Register new employee
    $result = $employee->registerEmployee($input);
    
    if ($result['success']) {
        http_response_code(201);
    } else {
        http_response_code(400);
    }
    
    echo json_encode($result);
}

function handlePutRequest($employee) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data'
        ]);
        return;
    }
    
    // Update employee status
    $result = $employee->updateEmployeeStatus($input);
    
    if (!$result['success']) {
        http_response_code(400);
    }
    
    echo json_encode($result);
}
?>