<?php
session_start();

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $conn = new mysqli("localhost", "root", "", "fast_thrift");
    
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);
    
    $result = $conn->query("SELECT * FROM admins WHERE email = '$email' AND password = '$password' LIMIT 1");
    
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin.php");
        exit;
    }
    
    $error = "Invalid credentials";
    $conn->close();
}

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    // Show login form if not logged in
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login - Fast Thrift</title>
        <style>
            body {
                background-color: #2b2b2b;
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
            }
            .login-container {
                max-width: 400px;
                margin: 100px auto;
                padding: 20px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.2);
            }
            .form-group {
                margin-bottom: 15px;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                color: #333;
            }
            .form-group input {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 20px;
            }
            .submit-btn {
                background: #ffd700;
                color: #333;
                padding: 10px;
                border: none;
                border-radius: 20px;
                cursor: pointer;
                width: 100%;
                font-weight: bold;
            }
            .submit-btn:hover {
                background: #ffed4a;
            }
            .error {
                color: #dc3545;
                margin-bottom: 10px;
            }
            h2 {
                color: #333;
                text-align: center;
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h2>Admin Login</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="login" class="submit-btn">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "fast_thrift");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $id = $conn->real_escape_string($_POST['product_id']);
    $qty = intval($_POST['quantity']);
    $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
    $stmt->bind_param("is", $qty, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit;
}

// Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Products</title>
    <style>
        body {
            background-color: #2b2b2b;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .page-title {
            color: #fff;
            text-align: center;
            padding: 20px 0;
            font-size: 32px;
            margin-bottom: 30px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        .nav-buttons {
            text-align: center;
            margin-bottom: 30px;
        }
        .nav-btn {
            display: inline-block;
            background: #ffd700;
            color: #333;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .nav-btn:hover {
            background: #ffed4a;
        }
        .logout-btn {
            background: #dc3545;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        img {
            max-width: 60px;
            max-height: 60px;
            border-radius: 4px;
        }
        input[type="number"] {
            width: 70px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin-right: 5px;
            text-align: center;
        }
        button[type="submit"] {
            background: #ffd700;
            color: #333;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        button[type="submit"]:hover {
            background: #ffed4a;
        }
        .price {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1 class="page-title">Admin: Manage Products</h1>
    
    <div class="nav-buttons">
        <a href="index.php" class="nav-btn">Home</a>
        <a href="cart.php" class="nav-btn">Cart</a>
        <a href="admin.php?logout=1" class="nav-btn logout-btn">Logout</a>
    </div>

    <div class="container">
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Price (₱)</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Update Quantity</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td class="price">₱<?= number_format($row['price'], 2) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>
                    <?php if (!empty($row['image'])): ?>
                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="Image">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="update_quantity" value="1">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($row['id']) ?>">
                        <input type="number" name="quantity" value="<?= $row['quantity'] ?>" min="0" required>
                        <button type="submit" name="submit_update">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>