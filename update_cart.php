
<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Неавторизований користувач']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id = intval($data['id'] ?? 0);
$quantity = intval($data['quantity'] ?? 1);

if ($id <= 0 || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Невірні дані']);
    exit;
}

if (!isset($_SESSION['cart'][$id])) {
    echo json_encode(['success' => false, 'message' => 'Товар відсутній у кошику']);
    exit;
}

$_SESSION['cart'][$id] = $quantity;

// Оновлюємо загальну вартість
$host = 'localhost';
$user = 'root';
$password = 'smaik1322';
$dbname = 'shop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Помилка підключення до БД']);
    exit;
}

$cart = $_SESSION['cart'];
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
