<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$user = 'root';

$password = '';

$dbname = 'shop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

$cart = $_SESSION['cart'] ?? [];
$products = [];
$error = '';
$orderSuccess = false; // прапорець успішного замовлення

// Обробка оформлення замовлення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy'])) {
    if (empty($cart)) {
        $error = "Ваш кошик порожній.";
    } else {
        foreach ($cart as $productId => $quantity) {
            // Вибираємо назву товару
            $stmt = $conn->prepare("SELECT title FROM books WHERE id = ?");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $stmt->bind_result($productName);
            $stmt->fetch();
            $stmt->close();

            // Записуємо замовлення в таблицю orders
            $userId = $_SESSION['user_id'];
            $status = 'Новий';

            $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, quantity, status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isis", $userId, $productName, $quantity, $status);
            $stmt->execute();
            $stmt->close();
        }

        // Очищаємо кошик після покупки
        unset($_SESSION['cart']);

        // Встановлюємо прапорець успішного замовлення
        $orderSuccess = true;
        // Оновлюємо змінну $cart, бо він уже пустий
        $cart = [];
    }
}

// Підготовка товарів для відображення в кошику
if ($cart) {
    $ids = implode(',', array_keys($cart));
    $sql = "SELECT id, title, author, image, price FROM books WHERE id IN ($ids)";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[$row['id']] = $row;
        }
    }
}

$cart_items = [];
$total_price = 0;

foreach ($cart as $id => $quantity) {
    if (isset($products[$id])) {
        $item = $products[$id];
        $item['quantity'] = $quantity;
        $total_price += $item['price'] * $quantity;
        $cart_items[] = $item;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <title>Кошик</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/cart.css" />
</head>
<body>
<?php include 'header.php'; ?>

<div class="cart-container">
  <h2 class="cart-title">Ваш кошик</h2>

  <?php if ($orderSuccess): ?>
    <div class="success-box" style="background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:15px;">
      Замовлення успішно оформлено. Дякуємо вам!
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error-box"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (empty($cart_items)): ?>
    <p class="cart-empty">Кошик порожній</p>
  <?php else: ?>
    <ul class="cart-items">
      <?php foreach ($cart_items as $item): ?>
        <li class="cart-item" data-id="<?= $item['id'] ?>">
          <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
          <div class="cart-item-details">
            <h4><?= htmlspecialchars($item['title']) ?></h4>
            <p>Автор: <?= htmlspecialchars($item['author']) ?></p>
            <p>Ціна за одиницю: <?= number_format($item['price'], 2) ?> грн</p>
            <div class="cart-item-quantity">
              Кількість:
              <div class="quantity-control" data-id="<?= $item['id'] ?>">
                <button class="qty-btn minus">−</button>
                <input type="text" class="quantity-input" value="<?= $item['quantity'] ?>" readonly>
                <button class="qty-btn plus">+</button>
              </div>
            </div>
            <button class="cart-item-remove" data-id="<?= $item['id'] ?>">×</button>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="cart-total" id="cart-total">
      Загальна вартість: <?= number_format($total_price, 2) ?> грн
    </div>

    <form method="post" action="cart.php">
      <button type="submit" name="buy" class="btn">Купити</button>
    </form>
  <?php endif; ?>
</div>

<script src="js/cart.js"></script>
</body>
</html>
