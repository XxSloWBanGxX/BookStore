<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Користувач не авторизований']);
    exit;
}

if (empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Кошик порожній']);
    exit;
}

$host = 'localhost';
$user = 'root';
<<<<<<< HEAD
$password = '';
=======
$password = 'smaik1322';
>>>>>>> bb325550c9229aaa4ba21cf919905c8fa94a2afb
$dbname = 'shop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Помилка підключення до бази']);
    exit;
}

$cart = $_SESSION['cart'];
$userId = $_SESSION['user_id'];

foreach ($cart as $productId => $quantity) {
    // Вибираємо назву товару
    $stmt = $conn->prepare("SELECT title FROM books WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($productName);
    $stmt->fetch();
    $stmt->close();

    if (!$productName) {
        // Якщо товар не знайдено — пропускаємо
        continue;
    }

    // Записуємо замовлення в таблицю orders
    $status = 'Новий';

    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, quantity, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $userId, $productName, $quantity, $status);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Очищаємо кошик після покупки
unset($_SESSION['cart']);

echo json_encode(['success' => true, 'message' => 'Замовлення успішно оформлено']);
