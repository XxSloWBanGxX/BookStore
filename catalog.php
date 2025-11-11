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

/* ---------- Пошук + пагінація (10 на сторінку) ---------- */
$q     = isset($_GET['q'])    ? trim($_GET['q'])    : '';
$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per   = 10;                               // ФІКСОВАНО: максимум 10 на сторінку
$offset = ($page - 1) * $per;

/* Допоміжна: будуємо URL, зберігаючи q */
function build_url($params = []) {
    $base = strtok($_SERVER["REQUEST_URI"], '?') ?: 'catalog.php';
    $current = [
        'q'    => isset($_GET['q']) ? $_GET['q'] : null,
        'page' => null, // завжди перезаписуємо
    ];
    $merged = array_filter(array_merge($current, $params), fn($v) => $v !== null && $v !== '');
    return htmlspecialchars($base . '?' . http_build_query($merged), ENT_QUOTES, 'UTF-8');
}

/* ---------- Підрахунок total ---------- */
if ($q !== '') {
    $like = '%' . $q . '%';
    $sql_count = "SELECT COUNT(*) AS cnt
                  FROM books
                  WHERE title LIKE ? OR author LIKE ?";
    $stmt_cnt = $conn->prepare($sql_count);
    if (!$stmt_cnt) die("Помилка підготовки COUNT: " . $conn->error);
    $stmt_cnt->bind_param("ss", $like, $like);
    $stmt_cnt->execute();
    $total = (int)$stmt_cnt->get_result()->fetch_assoc()['cnt'];
    $stmt_cnt->close();

    // Вибірка з LIMIT/OFFSET
    $sql = "SELECT id, title, author, description, image, price
            FROM books
            WHERE title LIKE ? OR author LIKE ?
            ORDER BY title ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Помилка підготовки SELECT: " . $conn->error);
    $stmt->bind_param("ssii", $like, $like, $per, $offset);
    $stmt->execute();
    $result_catalog = $stmt->get_result();

} else {
    $res_cnt = $conn->query("SELECT COUNT(*) AS cnt FROM books");
    $total = $res_cnt ? (int)$res_cnt->fetch_assoc()['cnt'] : 0;

    $sql = "SELECT id, title, author, description, image, price
            FROM books
            ORDER BY title ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Помилка підготовки SELECT: " . $conn->error);
    $stmt->bind_param("ii", $per, $offset);
    $stmt->execute();
    $result_catalog = $stmt->get_result();
}

$total_pages = max(1, (int)ceil($total / $per));
if ($page > $total_pages && $total_pages > 0) {
    header("Location: " . build_url(['page' => $total_pages]));
    exit;
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
    .search-bar { display:flex; gap:.5rem; align-items:center; margin:1rem 0 1.25rem; }
    .search-bar input[type="text"] { flex:1; padding:.6rem .8rem; border:1px solid #ddd; border-radius:8px; }
    .search-bar button { padding:.6rem 1rem; border:0; border-radius:8px; background:#1f6feb; color:#fff; cursor:pointer; }
    .search-meta { color:#666; font-size:.9rem; margin-bottom:.75rem; }
    .no-results { padding:1rem; background:#fff8d6; border:1px solid #f1e3a3; border-radius:10px; }

    /* Пагінація внизу */
    .pagination{ margin:2rem 0 1rem; display:flex; justify-content:center; align-items:center; gap:.4rem; flex-wrap:wrap; }
    .pagination a, .pagination span{
      display:inline-block; min-width:38px; padding:.5rem .75rem; border:1px solid #e1e1e1;
      border-radius:8px; text-decoration:none; color:#333; background:#fff; text-align:center;
    }
    .pagination a:hover{ border-color:#bbb; }
    .pagination .active{ background:#1f6feb; color:#fff; border-color:#1f6feb; cursor:default; }
    .pagination .disabled{ color:#999; border-color:#eee; background:#fafafa; pointer-events:none; }
  </style>

  <?php if ($page > 1): ?>
    <link rel="prev" href="<?= build_url(['page' => $page - 1]) ?>">
  <?php endif; ?>
  <?php if ($page < $total_pages): ?>
    <link rel="next" href="<?= build_url(['page' => $page + 1]) ?>">
  <?php endif; ?>
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
    <div class="search-meta">Знайдено: <strong><?= (int)$total ?></strong> • Запит: <strong><?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?></strong></div>
  <?php else: ?>
    <div class="search-meta">Всього книг: <strong><?= (int)$total ?></strong></div>
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
        <?php else: ?>
          Книг поки що немає.
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </div>

  <!-- Пагінація внизу -->
  <?php if ($total_pages > 1): ?>
    <nav class="pagination" aria-label="Навігація сторінками">
      <?php
        $prev = $page - 1;
        $next = $page + 1;

        echo '<a class="'.($page<=1?'disabled':'').'" href="'.($page<=1?'#':build_url(['page'=>$prev])).'">« Назад</a>';

        $window = 2;
        $start = max(1, $page - $window);
        $end   = min($total_pages, $page + $window);

        if ($start > 1) {
          echo '<a href="'.build_url(['page'=>1]).'">1</a>';
          if ($start > 2) echo '<span class="disabled">…</span>';
        }

        for ($p = $start; $p <= $end; $p++) {
          if ($p == $page) {
            echo '<span class="active">'.$p.'</span>';
          } else {
            echo '<a href="'.build_url(['page'=>$p]).'">'.$p.'</a>';
          }
        }

        if ($end < $total_pages) {
          if ($end < $total_pages - 1) echo '<span class="disabled">…</span>';
          echo '<a href="'.build_url(['page'=>$total_pages]).'">'.$total_pages.'</a>';
        }

        echo '<a class="'.($page>=$total_pages?'disabled':'').'" href="'.($page>=$total_pages?'#':build_url(['page'=>$next])).'">Вперед »</a>';
      ?>
    </nav>
  <?php endif; ?>
</main>

<script src="js/catalog.js"></script>
</body>
</html>

<?php
if (isset($stmt) && $stmt instanceof mysqli_stmt) { $stmt->close(); }
$conn->close();
?>
