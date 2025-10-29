<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);

if ($id <= 0 || !isset($_SESSION['cart'][$id])) {
    echo json_encode(['success' => false]);
    exit;
}

unset($_SESSION['cart'][$id]);

// Оновлюємо загальну суму
$host = 'localhost';
$user = 'root';
$password = 'smaik1322';
$dbname = 'shop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false]);
    exit;
}

$cart = $_SESSION['cart'];
if (empty($cart)) {
    echo json_encode(['success' => true, 'total' => 0]);
    exit;
}

$ids = implode(',', array_keys($cart));
$sql = "SELECT id, price FROM books WHERE id IN ($ids)";
$result = $conn->query($sql);

$total = 0;
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $total += $row['price'] * $cart[$row['id']];
    }
}

echo json_encode(['success' => true, 'total' => $total]);
