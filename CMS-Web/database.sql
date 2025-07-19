-- Church CMS Database Structure

-- Create database
CREATE DATABASE IF NOT EXISTS church_cms;
USE church_cms;

-- Content table for sermons and testimonies
CREATE TABLE content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('sermon', 'testimony') NOT NULL,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    views INT DEFAULT 0,
    downloads INT DEFAULT 0,
    likes INT DEFAULT 0, -- Added likes column
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users table for admin authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pastor', 'department_leader', 'member') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Departments table
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    leader_id INT,
    member_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Announcements table
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT,
    department_id INT,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    publish_date DATETIME,
    expiry_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    pastor_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    appointment_date DATETIME NOT NULL,
    duration INT DEFAULT 60, -- in minutes
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pastor_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Financial records table
CREATE TABLE financial_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('tithe', 'offering', 'donation', 'project') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    donor_name VARCHAR(255),
    donor_email VARCHAR(100),
    department_id INT,
    description TEXT,
    payment_method ENUM('cash', 'check', 'online', 'bank_transfer') DEFAULT 'cash',
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password, role, first_name, last_name) 
VALUES ('admin', 'admin@churchcms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Admin', 'User');

-- Insert sample departments
INSERT INTO departments (name, description) VALUES 
('Pastoral Department', 'Oversees spiritual leadership and pastoral care'),
('Worship Team', 'Manages music and worship services'),
('Youth Ministry', 'Focuses on young people and their spiritual growth'),
('Children Ministry', 'Cares for children and their spiritual development'),
('Ushering', 'Manages church services and events'),
('Technical Team', 'Handles audio, video, and technical aspects');

-- Insert sample content
INSERT INTO content (type, title, author, description, category, file_path, file_type) VALUES 
('sermon', 'Walking in Faith', 'Pastor John Smith', 'A powerful message about walking in faith through life\'s challenges. Learn how to trust in God\'s plan even when the path seems unclear.', 'faith', 'uploads/sermon1.mp4', 'video'),
('sermon', 'The Power of Prayer', 'Pastor Sarah Johnson', 'Discover the transformative power of prayer in our daily lives. This sermon explores how prayer can change our circumstances and strengthen our relationship with God.', 'prayer', 'uploads/sermon2.mp3', 'audio'),
('sermon', 'Building Strong Families', 'Pastor Michael Brown', 'Biblical principles for building strong, God-centered families. Learn practical ways to strengthen family bonds and create a loving home environment.', 'family', 'uploads/sermon3.pdf', 'pdf'),
('testimony', 'Miracle of Healing', 'Sarah Williams', 'After years of struggling with chronic pain, I received prayer during our healing service. That night, I felt a warmth spread through my body, and the pain was completely gone. God is truly faithful!', 'healing', 'uploads/testimony1.mp4', 'video'),
('testimony', 'Financial Breakthrough', 'David Chen', 'I was struggling with debt and couldn\'t see a way out. After committing to faithful tithing and seeking God\'s guidance, I received an unexpected job offer with double my previous salary. God\'s provision is amazing!', 'financial', 'uploads/testimony2.mp3', 'audio'),
('testimony', 'Deliverance from Addiction', 'Maria Rodriguez', 'I was trapped in addiction for 10 years. Through the prayers of our church family and God\'s grace, I was completely delivered. Today I\'m free and helping others find the same freedom in Christ.', 'deliverance', 'uploads/testimony3.txt', 'text');

-- Create indexes for better performance
CREATE INDEX idx_content_type ON content(type);
CREATE INDEX idx_content_category ON content(category);
CREATE INDEX idx_content_upload_date ON content(upload_date);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_announcements_status ON announcements(status);
CREATE INDEX idx_appointments_status ON appointments(status); 