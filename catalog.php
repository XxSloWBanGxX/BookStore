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
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <title>Каталог книг</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/catalog.css" />
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
  <h2>Каталог книг</h2>
  <div class="catalog-grid" id="catalog-container">
    <?php
    $sql_catalog = "SELECT id, title, author, description, image, price FROM books";
    $result_catalog = $conn->query($sql_catalog);

    if ($result_catalog && $result_catalog->num_rows > 0):
      while($book = $result_catalog->fetch_assoc()):
    ?>
      <div class="catalog-card" data-id="<?= $book['id'] ?>">
        <img src="<?= htmlspecialchars($book['image']) ?>" alt="Обкладинка книги" class="book-image">
        <h3><?= htmlspecialchars($book['title']) ?></h3>
        <p><strong>Автор:</strong> <?= htmlspecialchars($book['author']) ?></p>
        <p><?= htmlspecialchars(mb_strimwidth($book["description"], 0, 60, '...')) ?></p>
        <p class="price">Ціна: <strong><?= number_format($book['price'], 2, ',', ' ') ?> грн</strong></p>
        <button class="btn add-to-cart">Додати до кошика</button>
      </div>
    <?php
      endwhile;
    else:
    ?>
      <p>Книг поки що немає.</p>
    <?php endif; ?>
  </div>
</main>

<script src="js/catalog.js"></script>
</body>
</html>

<?php
$conn->close();
?>