<?php
// Запускаємо сесію, якщо ще не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<header>
    <div class="container header-flex">
        <h1 class="site-title">
            <img src="img/logo.png" alt="Логотип" class="logo">Книжковий Світ
        </h1>
        <div class="right-side">
            <nav class="nav-links">
                <a href="index.php">Головна</a>
                <a href="catalog.php">Каталог</a>
                <a href="developer.php">Розробник</a>

                <!-- Якщо користувач адмін, показуємо посилання на адмін панель -->
                <?php if ($isAdmin): ?>
                    <a href="admin.php">Адмін панель</a>
                <?php endif; ?>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="auth-box">
                        <span>Привіт, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        <a href="cart.php" class="btn black-btn">Кошик (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)</a>
                        <a href="logout.php" class="btn black-btn">Вихід</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn black-btn">Вхід</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

