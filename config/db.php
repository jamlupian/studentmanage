<?php
// Folder name inside htdocs. Change this if you rename the project folder.
define('BASE_URL', '/student_management');

$DB_HOST = 'localhost';
$DB_NAME = 'student_management';
$DB_USER = 'root';   // XAMPP default
$DB_PASS = '';       // XAMPP default (empty)

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Could not connect to the database. Make sure MySQL is running in XAMPP and that you imported database.sql.');
}
