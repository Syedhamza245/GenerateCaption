<?php
// cors.php
header("Access-Control-Allow-Origin: http://localhost:3000"); // Allow only your frontend domain
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Allow POST, GET, OPTIONS methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
header("Access-Control-Allow-Credentials: true"); // Allow credentials like cookies or authorization headers
header("Content-Type: application/json"); // Set content type as JSON
?>