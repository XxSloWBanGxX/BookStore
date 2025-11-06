<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'shop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($username) || empty($password) || empty($password_confirm)) {
        $errors[] = 'Усі поля обов’язкові.';
    } elseif ($password !== $password_confirm) {
        $errors[] = 'Паролі не співпадають.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'Користувач з таким ім’ям вже існує.';
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $username, $password_hash, $role);
            if ($stmt_insert->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = 'Помилка реєстрації. Спробуйте пізніше.';
            }
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
    <title>Реєстрація</title>
    <link rel="stylesheet" href="css/register-login.css" />
</head>
<body>
    <div class="auth-container">
        <h2>Реєстрація</h2>

        <?php if ($errors): ?>
            <div class="error-box">
                <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="" class="auth-form">
            <label>Ім'я користувача:
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </label>
            <label>Пароль:
                <input type="password" name="password" required>
            </label>
            <label>Підтвердження пароля:
                <input type="password" name="password_confirm" required>
            </label>
            <button type="submit" class="btn">Зареєструватися</button>
        </form>
        <p class="redirect">Вже маєте акаунт? <a href="login.php">Увійти</a></p>
        <div class="go-home">
    <a href="index.php" class="btn btn-secondary">На головну</a>
        </div>
    </div>
</body>
</html>
