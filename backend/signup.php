<?php
// Start output buffering to prevent unintended output
ob_start();

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json"); // Ensure JSON response

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
if (!file_exists('db.php')) {
    ob_end_clean(); // Clean any buffered output
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection file missing.'
    ]);
    exit;
}
include('db.php');

// Utility function to handle error responses
function handleError($message) {
    // Send error response
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

// Respond to OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    ob_end_clean(); // Clean any buffered output
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit;
}

// Validate and handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and decode JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate input
    if (!$input) {
        handleError('Invalid JSON input.');
    }

    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $confirm_password = $input['confirm_password'] ?? '';

    // Check if all fields are filled
    if (empty($email) || empty($password) || empty($confirm_password)) {
        handleError('All fields are required.');
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        handleError('Password and Confirm Password do not match.');
    }

    // Sanitize inputs to prevent SQL injection
    $email = mysqli_real_escape_string($con, $email);

    // Check if the email already exists
    $existanceQuery = "SELECT * FROM users WHERE email='$email'";
    $checkEmailExist = mysqli_query($con, $existanceQuery);
    if (mysqli_num_rows($checkEmailExist) > 0) {
        handleError('User already exists.');
    }

    // Hash the password for secure storage
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert the new user into the database
    $insertQuery = "INSERT INTO users (email, password, date) VALUES ('$email', '$hashedPassword', current_timestamp())";

    if (!mysqli_query($con, $insertQuery)) {
        handleError('Database error: ' . mysqli_error($con));
    }

    // Success response
    ob_end_clean(); // Clean any buffered output before sending final JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'You have registered successfully!',
        'data' => [
            'email' => $email
        ]
    ]);
    exit;
}

// Fallback for unsupported methods
handleError('Unsupported request method.');
?>

