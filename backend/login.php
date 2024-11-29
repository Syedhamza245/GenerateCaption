    <?php
    header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json");

    include('db.php');

    function handleError($message) {
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            handleError('All fields are required..!');
        }

        $email = mysqli_real_escape_string($con, $email);

        $checkEmailExist = "SELECT * FROM users WHERE email='$email'";
        if (!$result = mysqli_query($con, $checkEmailExist)) {
            handleError('Database error: ' . mysqli_error($con));
        }

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user['password'])) {
                $token = bin2hex(random_bytes(16));
                $token = mysqli_real_escape_string($con, $token);

                $insertToken = "UPDATE users SET token='$token' WHERE id={$user['id']}";
                if (!$fired = mysqli_query($con, $insertToken)) {
                    handleError('Failed to update token in database: ' . mysqli_error($con));
                }

                // Set the token in a cookie
                $cookieName = "auth_token";
                $cookieValue = $token;
                $cookieExpiration = time() + (86400 * 30); // Cookie expires in 30 days
                $cookiePath = "/"; // Accessible across the entire domain
                $secure = false; // Send only over HTTPS
                $httpOnly = true; // Prevent JavaScript access
                $samesite = "Lax";
                setcookie($cookieName, $cookieValue, $cookieExpiration, $cookiePath, $secure, $httpOnly, $samesite);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'You logged in successfully..!',
                    'token' => $token,
                    'data' => [
                        'id' => $user['id'],
                        'email' => $user['email']
                    ]
                ]);
            } else {
                handleError('Invalid password.');
            }
        } else {
            handleError('Email not found.');
        }
    } else {
        handleError('Invalid request method.');
    }
    ?>