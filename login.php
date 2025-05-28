<?php
session_start();
header('Content-Type: application/json');

$response = [
    'success' => false, 
    'message' => 'Invalid email or password.',
    'redirect' => 'index.php'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = new mysqli("localhost", "root", "", "fast_thrift");
    if ($conn->connect_error) {
        $response['message'] = "Connection failed: " . $conn->connect_error;
    } else {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $first_name, $last_name, $db_email, $db_password);
            $stmt->fetch();

            if (password_verify($password, $db_password)) {
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $db_email;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['user'] = [
                    'email' => $db_email,
                    'first_name' => $first_name,
                    'last_name' => $last_name
                ];
                $response['success'] = true;
                $response['message'] = "Login successful!";
                $response['email'] = $db_email;
                $response['first_name'] = $first_name;
                $response['last_name'] = $last_name;
            }
        }
        $stmt->close();
        $conn->close();
    }
}

echo json_encode($response);
exit;