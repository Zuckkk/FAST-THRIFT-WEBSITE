<?php
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Registration failed.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    if ($password !== $confirm_password) {
        $response['message'] = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $conn = new mysqli("localhost", "root", "", "fast_thrift");
        if ($conn->connect_error) {
            $response['message'] = "Connection failed: " . $conn->connect_error;
        } else {
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                $response['message'] = "Email already registered. Please use a different email.";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $email, $hashed_password, $first_name, $last_name);
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Registration successful!";
                } else {
                    $response['message'] = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
            $check_stmt->close();
            $conn->close();
        }
    }
}
echo json_encode($response);
exit;