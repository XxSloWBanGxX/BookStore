<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
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

// Обробка зміни статусу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit;
}

// Обробка видалення замовлення
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order_id'])) {
    $deleteOrderId = (int)$_POST['delete_order_id'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $deleteOrderId);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit;
}

$sql = "SELECT o.id, o.product_name, o.quantity, o.status, o.created_at, u.username 
        FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Адмін панель - Замовлення</title>
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<?php include 'header.php'; ?>

<div class="container admin-container">
    <h1 class="page-title">Адмін панель — Управління замовленнями</h1>

    <table class="orders-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Користувач</th>
                <th>Товар</th>
                <th>Кількість</th>
                <th>Статус</th>
                <th>Дата замовлення</th>
                <th>Змінити статус</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
    <!-- Форма зміни статусу -->
    <form method="post" action="admin.php" class="status-form" style="display:inline-block; margin-right: 10px;">
        <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
        <select name="new_status" class="status-select">
            <option value="Новий" <?= $row['status'] === 'Новий' ? 'selected' : '' ?>>Новий</option>
            <option value="В обробці" <?= $row['status'] === 'В обробці' ? 'selected' : '' ?>>В обробці</option>
            <option value="Доставляється" <?= $row['status'] === 'Доставляється' ? 'selected' : '' ?>>Доставляється</option>
            <option value="Виконано" <?= $row['status'] === 'Виконано' ? 'selected' : '' ?>>Виконано</option>
            <option value="Скасовано" <?= $row['status'] === 'Скасовано' ? 'selected' : '' ?>>Скасовано</option>
        </select>
        <button type="submit" class="btn btn-small">Змінити</button>
    </form>

    <!-- Форма видалення -->
    <form method="post" action="admin.php" class="delete-form" style="display:inline-block;">
        <input type="hidden" name="delete_order_id" value="<?= $row['id'] ?>">
        <button type="submit" class="btn btn-delete" onclick="return confirm('Ви впевнені, що хочете видалити це замовлення? Цю дію не можна скасувати.');">Видалити</button>
    </form>
</td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">Замовлень поки що немає.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
