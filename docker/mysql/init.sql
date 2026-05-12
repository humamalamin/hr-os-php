CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'editor', 'viewer') DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(50),
    position VARCHAR(100),
    department VARCHAR(100),
    status ENUM('active', 'inactive', 'onboarding') DEFAULT 'onboarding',
    address TEXT,
    joining_date DATE,
    photo_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX (full_name),
    INDEX (department)
);

-- Seed some data
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO employees (employee_id, full_name, email, phone, position, department, status, joining_date) VALUES 
('EMP-00124', 'John Doe', 'john.doe@company.com', '+1 (555) 000-1234', 'Senior Software Engineer', 'Engineering', 'active', '2024-05-10'),
('EMP-00125', 'Sarah Smith', 'sarah.s@company.com', '+1 (555) 000-5678', 'UI/UX Designer', 'Design', 'active', '2024-05-08');
