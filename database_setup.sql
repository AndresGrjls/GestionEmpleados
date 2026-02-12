CREATE DATABASE IF NOT EXISTS employee_management 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE employee_management;

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
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_email (email),
    INDEX idx_name (firstName, lastName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO employees (firstName, lastName, email, position, salary, hireDate, status) VALUES
('Leo', 'Lion', 'leo.lion@animalkingdom.com', 'CEO', 150000.00, '2020-01-15', 'active'),
('Ella', 'Elephant', 'ella.elephant@animalkingdom.com', 'HR Manager', 85000.00, '2020-03-20', 'active'),
('Max', 'Monkey', 'max.monkey@animalkingdom.com', 'Developer', 75000.00, '2021-06-10', 'active'),
('Bella', 'Bear', 'bella.bear@animalkingdom.com', 'Designer', 70000.00, '2021-08-05', 'inactive'),
('Charlie', 'Cheetah', 'charlie.cheetah@animalkingdom.com', 'Sales Manager', 80000.00, '2019-11-12', 'terminated');

UPDATE employees 
SET terminationDate = '2023-12-31' 
WHERE status = 'terminated';