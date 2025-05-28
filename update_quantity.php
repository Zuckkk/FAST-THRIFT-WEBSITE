<?php
session_start();
if (isset($_SESSION['user'])): ?>
<script>
localStorage.setItem("user", JSON.stringify({
    email: "<?php echo $_SESSION['user']['email']; ?>",
    first_name: "<?php echo $_SESSION['user']['first_name']; ?>",
    last_name: "<?php echo $_SESSION['user']['last_name']; ?>"
}));
</script>
<?php endif; ?>
<?php
file_put_contents('debug.log', print_r($_POST, true), FILE_APPEND);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $conn = new mysqli("localhost", "root", "", "fast_thrift");
    $success = false;
    $quantity = 0;
    if (!$conn->connect_error) {
        $conn->query("UPDATE products SET quantity = quantity - 1 WHERE id = '$id' AND quantity > 0");
        $result = $conn->query("SELECT quantity FROM products WHERE id = '$id'");
        if ($row = $result->fetch_assoc()) {
            $quantity = (int)$row['quantity'];
            $success = true;
        }
        $conn->close();
    }
    echo json_encode(['success' => $success, 'quantity' => $quantity]);
    exit;
}
echo json_encode(['success' => false]);
