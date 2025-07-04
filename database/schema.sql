-- Create the database
CREATE DATABASE IF NOT EXISTS expense_control CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE expense_control;

-- Create the user table
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create the profile table
CREATE TABLE IF NOT EXISTS profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    position_or_company VARCHAR(255),
    marital_status VARCHAR(50),
    children INT DEFAULT 0,
    assets DECIMAL(15, 2) DEFAULT 0.00,
    initial_balance DECIMAL(15, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES user(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create the expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    date DATE NOT NULL,
    description VARCHAR(255),
    amount DECIMAL(15, 2) NOT NULL,
    type VARCHAR(50),
    FOREIGN KEY (profile_id) REFERENCES profile(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create the income table
CREATE TABLE IF NOT EXISTS income (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    date DATE NOT NULL,
    description VARCHAR(255),
    amount DECIMAL(15, 2) NOT NULL,
    type VARCHAR(50),
    FOREIGN KEY (profile_id) REFERENCES profile(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;