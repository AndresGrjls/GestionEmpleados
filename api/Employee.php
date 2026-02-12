<?php
require_once 'database.php';

class Employee {
    private $db;
    private $connection;
    
    public function __construct() {
        $this->db = new Database();
        $this->connection = $this->db->getConnection();
        $this->db->createTables();
    }
    
    // Register new employee
    public function registerEmployee($data) {
        try {
            // Validate required fields
            $requiredFields = ['firstName', 'lastName', 'email', 'position', 'salary', 'hireDate'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field '{$field}' is required");
                }
            }
            
            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }
            
            // Validate salary
            if (!is_numeric($data['salary']) || $data['salary'] < 0) {
                throw new Exception("Salary must be a valid positive number");
            }
            
            // Validate names (no numbers)
            if (!preg_match('/^[a-zA-Z\s]+$/', $data['firstName'])) {
                throw new Exception("First name should contain only letters");
            }
            
            if (!preg_match('/^[a-zA-Z\s]+$/', $data['lastName'])) {
                throw new Exception("Last name should contain only letters");
            }
            
            // Validate hire date
            $hireDate = new DateTime($data['hireDate']);
            $today = new DateTime();
            if ($hireDate > $today) {
                throw new Exception("Hire date cannot be in the future");
            }
            
            // Check if email already exists
            $checkEmailSql = "SELECT id FROM employees WHERE email = :email";
            $checkStmt = $this->connection->prepare($checkEmailSql);
            $checkStmt->bindParam(':email', $data['email']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                throw new Exception("Email already exists in the system");
            }
            
            // Insert new employee
            $sql = "INSERT INTO employees (firstName, lastName, email, position, salary, hireDate) 
                    VALUES (:firstName, :lastName, :email, :position, :salary, :hireDate)";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':firstName', $data['firstName']);
            $stmt->bindParam(':lastName', $data['lastName']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':position', $data['position']);
            $stmt->bindParam(':salary', $data['salary']);
            $stmt->bindParam(':hireDate', $data['hireDate']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Employee registered successfully',
                    'employeeId' => $this->connection->lastInsertId()
                ];
            } else {
                throw new Exception("Failed to register employee");
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Search employees
    public function searchEmployees($searchTerm) {
        try {
            $sql = "SELECT * FROM employees 
                    WHERE id = :searchId 
                    OR firstName LIKE :searchTerm 
                    OR lastName LIKE :searchTerm 
                    OR email LIKE :searchTerm 
                    OR position LIKE :searchTerm
                    ORDER BY firstName, lastName";
            
            $stmt = $this->connection->prepare($sql);
            
            // Check if search term is numeric (for ID search)
            $searchId = is_numeric($searchTerm) ? intval($searchTerm) : 0;
            $searchPattern = '%' . $searchTerm . '%';
            
            $stmt->bindParam(':searchId', $searchId);
            $stmt->bindParam(':searchTerm', $searchPattern);
            
            $stmt->execute();
            $employees = $stmt->fetchAll();
            
            return [
                'success' => true,
                'data' => $employees,
                'count' => count($employees)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Update employee status
    public function updateEmployeeStatus($data) {
        try {
            // Validate required fields
            if (empty($data['employeeId']) || !is_numeric($data['employeeId'])) {
                throw new Exception("Valid employee ID is required");
            }
            
            if (empty($data['status'])) {
                throw new Exception("Status is required");
            }
            
            // Validate status values
            $validStatuses = ['active', 'inactive', 'terminated'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new Exception("Invalid status value");
            }
            
            // Check if employee exists
            $checkSql = "SELECT id, status FROM employees WHERE id = :employeeId";
            $checkStmt = $this->connection->prepare($checkSql);
            $checkStmt->bindParam(':employeeId', $data['employeeId']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() === 0) {
                throw new Exception("Employee not found");
            }
            
            $currentEmployee = $checkStmt->fetch();
            
            // Validate termination date if status is terminated
            $terminationDate = null;
            if ($data['status'] === 'terminated') {
                if (empty($data['terminationDate'])) {
                    throw new Exception("Termination date is required when status is terminated");
                }
                
                $terminationDate = new DateTime($data['terminationDate']);
                $today = new DateTime();
                if ($terminationDate > $today) {
                    throw new Exception("Termination date cannot be in the future");
                }
                
                $terminationDate = $data['terminationDate'];
            }
            
            // Update employee status
            $sql = "UPDATE employees 
                    SET status = :status, terminationDate = :terminationDate 
                    WHERE id = :employeeId";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':terminationDate', $terminationDate);
            $stmt->bindParam(':employeeId', $data['employeeId']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Employee status updated successfully'
                ];
            } else {
                throw new Exception("Failed to update employee status");
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Get employee statistics
    public function getEmployeeStats() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive,
                        SUM(CASE WHEN status = 'terminated' THEN 1 ELSE 0 END) as terminated
                    FROM employees";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch();
            
            return [
                'success' => true,
                'data' => [
                    'total' => intval($stats['total']),
                    'active' => intval($stats['active']),
                    'inactive' => intval($stats['inactive']) + intval($stats['terminated']),
                    'terminated' => intval($stats['terminated'])
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    // Get employee by ID
    public function getEmployeeById($id) {
        try {
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception("Valid employee ID is required");
            }
            
            $sql = "SELECT * FROM employees WHERE id = :id";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $employee = $stmt->fetch();
            
            if (!$employee) {
                throw new Exception("Employee not found");
            }
            
            return [
                'success' => true,
                'data' => $employee
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?>