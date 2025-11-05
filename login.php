<?php
session_start();

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
    die("Помилка підключення: " . $conn->connect_error);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = 'Всі поля обов’язкові.';
    } else {
        // Підготовлений запит для безпеки
        // Замість
// $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");

// Пиши так:
$stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($id, $password_hash, $role);
    $stmt->fetch();

    if (password_verify($password, $password_hash)) {
        // Логін успішний - записуємо дані в сесію
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['user_id'] = $id;  // <-- додано!

        header('Location: index.php');
        exit;
    } else {
        $errors[] = 'Неправильний логін або пароль.';
    }
} else {
    $errors[] = 'Неправильний логін або пароль.';
}

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <title>Вхід</title>
    <link rel="stylesheet" href="css/register-login.css" />
</head>
<body>
    <div class="auth-container">
        <h2>Вхід</h2>

        <?php if (!empty($_GET['registered'])): ?>
            <div class="success-box" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px;">
                Реєстрація успішна! Тепер ви можете увійти.
            </div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="error-box" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="" class="auth-form">
            <label>
                Ім'я користувача:
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </label>

            <label>
                Пароль:
                <input type="password" name="password" required>
            </label>

            <button type="submit" class="btn">Увійти</button>
        </form>

        <div class="redirect">
            Ще немає акаунта? <a href="register.php">Зареєструватися</a>
        </div>

        <div class="go-home">
            <a href="index.php" class="btn btn-secondary">На головну</a>
        </div>
    </div>
</body>
</html>
