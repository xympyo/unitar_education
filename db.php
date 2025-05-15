<?php
// Database connection for EduBridge
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'edu_users';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?>
