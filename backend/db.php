<?php
// db.php
// Database configuration
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'auth-db';
// Create connection
$con = new mysqli($host, $user, $password, $database);
// Check connection
if ($con->connect_error) {
    die('Connection failed: ' . $con->connect_error);
}
// Debug message (REMOVE THIS LINE)
// echo "Database connected successfully...!!";
?>
