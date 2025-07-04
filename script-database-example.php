<?php

use App\Core\Database;
use App\Core\DotEnv;
use PDO;
use PDOException;

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
(new DotEnv(__DIR__ . '/.env'))->load();

$host = getenv('DB_HOST');
$database = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');

try {
    $dsn = "mysql:host=$host"; // No database specified here
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$database' created successfully\n";

    // Select the database
    $pdo->exec("USE `$database`");

    // Create the user table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table 'user' created successfully\n";

    // Create the profile table
    $pdo->exec("
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
    ");
    echo "Table 'profile' created successfully\n";

    // Create the expenses table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS expenses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        profile_id INT NOT NULL,
        date DATE NOT NULL,
        description VARCHAR(255),
        amount DECIMAL(15, 2) NOT NULL,
        type VARCHAR(50),
        FOREIGN KEY (profile_id) REFERENCES profile(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table 'expenses' created successfully\n";

    // Create the income table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS income (
        id INT AUTO_INCREMENT PRIMARY KEY,
        profile_id INT NOT NULL,
        date DATE NOT NULL,
        description VARCHAR(255),
        amount DECIMAL(15, 2) NOT NULL,
        type VARCHAR(50),
        FOREIGN KEY (profile_id) REFERENCES profile(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Table 'income' created successfully\n";

    echo "All tables created successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}