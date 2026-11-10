-- Student Management System
-- Import via phpMyAdmin (http://localhost/phpmyadmin) -> Import tab
CREATE DATABASE IF NOT EXISTS student_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_management;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','student') NOT NULL DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  student_no VARCHAR(30) NOT NULL UNIQUE,
  course VARCHAR(100) DEFAULT '',
  year_level TINYINT NOT NULL DEFAULT 1,
  phone VARCHAR(30) DEFAULT '',
  address VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin account:  admin@school.test / admin123  (change it after first login)
INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@school.test', '$2y$10$dxrC6nOz9nAVJRVd12ko2ug42kNNPmQKQyAqKVIB68j67azKAYVoO', 'admin');
