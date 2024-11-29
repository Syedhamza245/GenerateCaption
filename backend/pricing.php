<?php
// Include your database connection file
include('cors.php'); // Ensure JSON response
include('db.php');

header('Content-Type: application/json');

// Retrieve token from cookies
$token = isset($_COOKIE['auth_token']) ? trim($_COOKIE['auth_token']) : ''; // Trim to avoid leading/trailing spaces

// Check if token is missing
if (empty($token)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Token is missing. Please log in again.'
    ]);
    exit;
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);
    $email = mysqli_real_escape_string($con, $data['email'] ?? '');
    $password = $data['password'] ?? ''; // Hash later
    $card_number = mysqli_real_escape_string($con, $data['card_number'] ?? '');
    $expiration_date = mysqli_real_escape_string($con, $data['expiration_date'] ?? '');
    $cvv = mysqli_real_escape_string($con, $data['cvv'] ?? '');
    $plan_name = mysqli_real_escape_string($con, $data['plan_name'] ?? '');

    // Validate if all required fields are provided
    if (empty($email) || empty($password) || empty($card_number) || empty($expiration_date) || empty($cvv) || empty($plan_name)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required!'
        ]);
        exit;
    }

    // Sanitize token
    $token = mysqli_real_escape_string($con, $token);

    // Validate token existence in the database and retrieve the user ID associated with the token
    $checkTokenQuery = "SELECT * FROM users WHERE token = '$token'";
    $result = mysqli_query($con, $checkTokenQuery);

    if (mysqli_num_rows($result) === 0) {
        // Token does not exist or is invalid
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid token. Please log in again.'
        ]);
        exit;
    }

    // Fetch the user from the database
    $user = mysqli_fetch_assoc($result);

    // Get the user_id from the database
    $user_id = $user['id'];

    // Hash the password securely
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Get the current timestamp for the start date
    $start_date = date('Y-m-d H:i:s');

    // Calculate the expiration date (1 month later)
    $end_date = date('Y-m-d H:i:s', strtotime('+1 month'));

    // SQL Query to insert subscription data into the user_subscriptions table
    $query = "INSERT INTO user_subscriptions (user_id, plan_name, start_date, end_date, status, email, password, card_number, expiration_date, cvv) 
              VALUES ('$user_id', '$plan_name', '$start_date', '$end_date', 'active', '$email', '$password_hashed', '$card_number', '$expiration_date', '$cvv')";

    // Execute the query
    if (mysqli_query($con, $query)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Subscription added successfully!',
        ]);
    } else {
        // Log the error for internal debugging
        error_log('Database error: ' . mysqli_error($con));
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add subscription. Please try again later.',
        ]);
    }
} else {
    // Handle non-POST requests
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Please use POST.'
    ]);
}
?>
