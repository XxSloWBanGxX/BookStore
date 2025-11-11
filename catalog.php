<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Параметри підключення до БД
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'shop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

// --- Параметри пошуку з GET ---
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$like = '%' . $q . '%';

// Якщо є пошук — використовуємо підготовлений запит
if ($q !== '') {
    $sql = "SELECT id, title, author, description, image, price
            FROM books
            WHERE title LIKE ? OR author LIKE ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Помилка підготовки запиту: " . $conn->error);
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result_catalog = $stmt->get_result();
} else {
    // Без пошуку — звичайний запит
    $sql = "SELECT id, title, author, description, image, price FROM books";
    $result_catalog = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <title>Каталог книг</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/catalog.css" />
  <style>
    /* легкий стиль для пошуку */
    .search-bar {
      display: flex; gap: .5rem; align-items: center; margin: 1rem 0 1.25rem;
    }
    .search-bar input[type="text"] {
      flex: 1; padding: .6rem .8rem; border: 1px solid #ddd; border-radius: 8px;
    }
    .search-bar button {
      padding: .6rem 1rem; border: 0; border-radius: 8px; background:#1f6feb; color:#fff; cursor:pointer;
    }
    .search-meta { color:#666; font-size:.9rem; margin-bottom: .75rem; }
    .no-results { padding:1rem; background:#fff8d6; border:1px solid #f1e3a3; border-radius:10px;}
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
  <h2>Каталог книг</h2>

  <!-- Пошук -->
  <form class="search-bar" action="" method="get" role="search">
    <input type="text" name="q" placeholder="Пошук за назвою або автором…" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit">Знайти</button>
    <?php if ($q !== ''): ?>
      <a href="catalog.php" style="margin-left:.5rem; text-decoration:none;">Скинути</a>
    <?php endif; ?>
  </form>

  <?php if ($q !== ''): ?>
    <div class="search-meta">Результати за запитом: <strong><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></strong></div>
  <?php endif; ?>

  <div class="catalog-grid" id="catalog-container">
    <?php if ($result_catalog && $result_catalog->num_rows > 0): ?>
      <?php while ($book = $result_catalog->fetch_assoc()): ?>
        <div class="catalog-card" data-id="<?= (int)$book['id'] ?>">
          <img src="<?= htmlspecialchars($book['image']) ?>" alt="Обкладинка книги" class="book-image">
          <h3><?= htmlspecialchars($book['title']) ?></h3>
          <p><strong>Автор:</strong> <?= htmlspecialchars($book['author']) ?></p>
          <p><?= htmlspecialchars(mb_strimwidth($book['description'], 0, 120, '…')) ?></p>
          <p class="price">Ціна: <strong><?= number_format((float)$book['price'], 2, ',', ' ') ?> грн</strong></p>
          <button class="btn add-to-cart">Додати до кошика</button>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="no-results">
        <?php if ($q !== ''): ?>
          За запитом <strong><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></strong> нічого не знайдено.
          Спробуйте іншу назву або автора.
        <?php else: ?>
          Книг поки що немає.
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </div>
</main>

<script src="js/catalog.js"></script>
</body>
</html>

<?php
if (isset($stmt) && $stmt instanceof mysqli_stmt) { $stmt->close(); }
$conn->close();
?>
