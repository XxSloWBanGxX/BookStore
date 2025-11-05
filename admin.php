<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$host = 'localhost';
$user = 'root';
<<<<<<< HEAD
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
=======
$password = 'smaik1322';
$dbname = 'shop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) die("Помилка підключення: " . $conn->connect_error);
$conn->set_charset('utf8mb4');

/* ===== Хелпери ===== */
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function postv($k,$d=null){ return $_POST[$k] ?? $d; }
function getv($k,$d=null){ return $_GET[$k] ?? $d; }

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];
$tab = getv('tab','dashboard');

/* ===== Обробка POST дій ===== */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])) {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $_SESSION['csrf']) exit('CSRF token mismatch');
    $action = $_POST['action'];

    // --- Додати книгу (з обкладинкою) ---
    if ($action === 'add_book') {
        $imagePath = null;

        if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $allowedExt = ['jpg','jpeg','png'];
            $maxBytes   = 5 * 1024 * 1024;

            $origName = $_FILES['image']['name'];
            $tmpPath  = $_FILES['image']['tmp_name'];
            $size     = (int)$_FILES['image']['size'];
            $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedExt, true)) exit('Неприпустимий формат файлу (JPG/PNG).');
            if ($size > $maxBytes) exit('Файл завеликий. Максимум 5 МБ.');

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
            if (!in_array($mime, ['image/jpeg','image/png'], true)) exit('Неприпустимий MIME-тип.');

            $uploadDir = __DIR__ . '/uploads/books';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

            $slugBase = preg_replace('~[^a-z0-9]+~i', '-', postv('title','book')) ?: 'book';
            $filename = $slugBase . '-' . bin2hex(random_bytes(4)) . '.' . $ext;

            $destFs  = $uploadDir . '/' . $filename;
            $destUrl = 'uploads/books/' . $filename;

            if (!move_uploaded_file($tmpPath, $destFs)) exit('Не вдалось зберегти файл обкладинки.');
            $imagePath = $destUrl;
        }

        $stmt = $conn->prepare("
            INSERT INTO books (title, author, price, stock, category_id, description, image)
            VALUES (?,?,?,?,?,?,?)
        ");
        $stmt->bind_param(
            "ssdiiss",
            $_POST['title'],
            $_POST['author'],
            $_POST['price'],
            $_POST['stock'],
            $_POST['category_id'],
            $_POST['description'],
            $imagePath
        );
        $stmt->execute(); $stmt->close();
    }

    // --- Видалити книгу ---
    if ($action === 'delete_book') {
        $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute(); $stmt->close();
    }

    // --- Додати категорію ---
    if ($action === 'add_category') {
        $stmt = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?,?)");
        $stmt->bind_param("ss", $_POST['name'], $_POST['slug']);
        $stmt->execute(); $stmt->close();
    }

    // --- Оновити статус замовлення ---
    if ($action === 'update_order_status') {
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $_POST['status'], $_POST['order_id']);
        $stmt->execute(); $stmt->close();
    }

    // --- Видалити користувача ---
    if ($action === 'delete_user') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $_POST['user_id']);
        $stmt->execute(); $stmt->close();
    }

    header("Location: admin.php?tab=".$tab);
    exit;
}

/* ===== Дані для вкладок ===== */
function fetchAll($conn,$sql){ $res=$conn->query($sql); if(!$res) die("SQL error: ".$conn->error); return $res; }

/* КНИГИ, КАТЕГОРІЇ, КОРИСТУВАЧІ, ВІДГУКИ */
$books = fetchAll($conn, "SELECT b.*, c.name AS category FROM books b LEFT JOIN categories c ON b.category_id=c.id ORDER BY b.id DESC");
$categories = fetchAll($conn, "SELECT * FROM categories ORDER BY name");
$users = fetchAll($conn, "SELECT * FROM users ORDER BY id DESC");
$reviews = fetchAll($conn, "SELECT r.*, u.username, b.title FROM reviews r JOIN users u ON u.id=r.user_id JOIN books b ON b.id=r.book_id ORDER BY r.id DESC");

/* ===== ЗАМОВЛЕННЯ з сумою total = SUM(oi.price * oi.qty) ===== */
$orders = fetchAll($conn, "
  SELECT 
    o.id, o.user_id, o.status, o.created_at,
    u.username,
    COALESCE((
      SELECT SUM(oi.price * oi.qty)
      FROM order_items oi
      WHERE oi.order_id = o.id
    ), 0) AS total
  FROM orders o
  JOIN users u ON u.id = o.user_id
  ORDER BY o.id DESC
");
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <title>Адмін панель — Книжковий Світ</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .admin-layout{display:grid;grid-template-columns:240px 1fr;gap:20px;margin-top:20px;}
    .admin-sidebar{background:#fff;border:1px solid #ddd;border-radius:12px;padding:14px;position:sticky;top:80px;height:fit-content;box-shadow:0 2px 8px rgba(0,0,0,0.06);}
    .sidebar-nav{display:flex;flex-direction:column;gap:14px;}
    .sidebar-group{display:flex;flex-direction:column;gap:6px;}
    .sidebar-group-title{font-size:12px;font-weight:800;color:#666;text-transform:uppercase;letter-spacing:.5px;padding:0 8px;margin-bottom:4px;}
    .sidebar-link{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;color:#333;text-decoration:none;border:1px solid transparent;transition:background .2s,border-color .2s;}
    .sidebar-link .dot{width:8px;height:8px;border-radius:50%;background:#bbb;flex:0 0 8px;transition:background .2s;}
    .sidebar-link:hover{background:#f7f7f7;border-color:#eee;}
    .sidebar-link.active{background:#111;color:#fff;border-color:#111;}
    .sidebar-link.active .dot{background:#fff;}
    .admin-content{background:#fff;border:1px solid #ddd;border-radius:12px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.06);}
    @media(max-width:960px){.admin-layout{grid-template-columns:1fr}.admin-sidebar{position:static}}
    .inline{display:inline-block;margin-right:8px;}
    .btn-delete{background:#b00020!important;color:#fff;}
    .btn-delete:hover{background:#d11a2a!important;}
    .card input[type="file"]{padding:8px;border:1px solid #ddd;border-radius:6px;background:#fafafa;}
    .cover-thumb{width:50px;height:70px;object-fit:cover;border-radius:4px;box-shadow:0 1px 3px rgba(0,0,0,.1);}
  </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container admin-layout">
  <!-- Ліва навігація -->
  <aside class="admin-sidebar">
    <nav class="sidebar-nav">
      <div class="sidebar-group">
        <div class="sidebar-group-title">Огляд</div>
        <a class="sidebar-link <?= $tab==='dashboard'?'active':'' ?>" href="admin.php?tab=dashboard"><span class="dot"></span> Дашборд</a>
        <a class="sidebar-link <?= $tab==='analytics'?'active':'' ?>" href="admin.php?tab=analytics"><span class="dot"></span> Аналітика</a>
        <a class="sidebar-link <?= $tab==='reports'?'active':'' ?>" href="admin.php?tab=reports"><span class="dot"></span> Звіти</a>
      </div>

      <div class="sidebar-group">
        <div class="sidebar-group-title">Каталог</div>
        <a class="sidebar-link <?= $tab==='books'?'active':'' ?>" href="admin.php?tab=books"><span class="dot"></span> Книги</a>
        <a class="sidebar-link <?= $tab==='categories'?'active':'' ?>" href="admin.php?tab=categories"><span class="dot"></span> Категорії</a>
        <a class="sidebar-link <?= $tab==='promos'?'active':'' ?>" href="admin.php?tab=promos"><span class="dot"></span> Акції</a>
      </div>

      <div class="sidebar-group">
        <div class="sidebar-group-title">Операції</div>
        <a class="sidebar-link <?= $tab==='orders'?'active':'' ?>" href="admin.php?tab=orders"><span class="dot"></span> Замовлення</a>
        <a class="sidebar-link <?= $tab==='reviews'?'active':'' ?>" href="admin.php?tab=reviews"><span class="dot"></span> Відгуки</a>
        <a class="sidebar-link <?= $tab==='users'?'active':'' ?>" href="admin.php?tab=users"><span class="dot"></span> Користувачі</a>
      </div>
    </nav>
  </aside>

  <!-- Контент справа -->
  <section class="admin-content">
    <h1 class="page-title">
      <?= [
        'dashboard'=>'Дашборд','books'=>'Книги','categories'=>'Категорії',
        'orders'=>'Замовлення','users'=>'Користувачі','reviews'=>'Відгуки',
        'promos'=>'Акції','reports'=>'Звіти','analytics'=>'Аналітика'
      ][$tab] ?? 'Адмін панель' ?>
    </h1>

    <?php if ($tab==='dashboard'): ?>
      <p>Оберіть розділ зліва.</p>

    <?php elseif ($tab==='books'): ?>
      <!-- Форма додавання книги (з обкладинкою) -->
      <form method="post" class="card" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="add_book">
        <label>Назва:</label><input name="title" required>
        <label>Автор:</label><input name="author" required>
        <label>Ціна:</label><input name="price" type="number" step="0.01" required>
        <label>Кількість:</label><input name="stock" type="number" required>
        <label>Категорія:</label>
        <select name="category_id">
          <option value="">—</option>
          <?php while($cat=$categories->fetch_assoc()): ?>
            <option value="<?= $cat['id'] ?>"><?= h($cat['name']) ?></option>
          <?php endwhile; $categories->data_seek(0); ?>
        </select>
        <label>Опис:</label><textarea name="description"></textarea>

        <label>Обкладинка (JPG/PNG, до 5 МБ):</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png">

        <button class="btn">Додати книгу</button>
      </form>

      <table class="orders-table">
        <tr>
          <th>ID</th><th>Обкладинка</th><th>Назва</th><th>Автор</th>
          <th>Категорія</th><th>Ціна</th><th>К-сть</th><th>Дії</th>
        </tr>
        <?php while($b=$books->fetch_assoc()): ?>
          <tr>
            <td><?= $b['id'] ?></td>
            <td>
              <?php if (!empty($b['image'])): ?>
                <img src="<?= h($b['image']) ?>" alt="cover" class="cover-thumb">
              <?php else: ?>
                <span style="color:#888;">—</span>
              <?php endif; ?>
            </td>
            <td><?= h($b['title']) ?></td>
            <td><?= h($b['author']) ?></td>
            <td><?= h($b['category']) ?></td>
            <td><?= number_format((float)$b['price'],2,'.',' ') ?> ₴</td>
            <td><?= (int)$b['stock'] ?></td>
            <td>
              <form class="inline" method="post">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="delete_book">
                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                <button class="btn btn-delete" onclick="return confirm('Видалити книгу?')">Видалити</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>

    <?php elseif ($tab==='categories'): ?>
      <form method="post" class="card">
        <input type="hidden" name="csrf" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="add_category">
        <label>Назва категорії:</label><input name="name" required>
        <label>Slug (англ.):</label><input name="slug">
        <button class="btn">Додати</button>
      </form>
      <table class="orders-table">
        <tr><th>ID</th><th>Назва</th><th>Slug</th></tr>
        <?php while($c=$categories->fetch_assoc()): ?>
          <tr><td><?= $c['id'] ?></td><td><?= h($c['name']) ?></td><td><?= h($c['slug']) ?></td></tr>
        <?php endwhile; ?>
      </table>

    <?php elseif ($tab==='orders'): ?>
      <table class="orders-table">
        <tr><th>ID</th><th>Користувач</th><th>Сума</th><th>Статус</th><th>Дата</th><th>Дії</th></tr>
        <?php while($o=$orders->fetch_assoc()): ?>
          <tr>
            <td><?= $o['id'] ?></td>
            <td><?= h($o['username']) ?></td>
            <td><?= number_format((float)($o['total'] ?? 0), 2, '.', ' ') ?> ₴</td>
            <td><?= h($o['status']) ?></td>
            <td><?= $o['created_at'] ?></td>
            <td>
              <form class="inline" method="post">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="update_order_status">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status">
                  <?php foreach(['Новий','В обробці','Доставляється','Виконано','Скасовано'] as $s): ?>
                    <option <?= $o['status']===$s?'selected':'' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn-small">Оновити</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>

    <?php elseif ($tab==='users'): ?>
      <table class="orders-table">
        <tr><th>ID</th><th>Ім’я</th><th>Email</th><th>Роль</th><th>Дата</th><th>Дії</th></tr>
        <?php while($u=$users->fetch_assoc()): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= h($u['username']) ?></td>
            <td><?= h($u['email']) ?></td>
            <td><?= h($u['role']) ?></td>
            <td><?= $u['created_at'] ?></td>
            <td>
              <form class="inline" method="post">
                <input type="hidden" name="csrf" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                <button class="btn btn-delete" onclick="return confirm('Видалити користувача?')">Видалити</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>

    <?php elseif ($tab==='reviews'): ?>
      <table class="orders-table">
        <tr><th>ID</th><th>Книга</th><th>Користувач</th><th>Оцінка</th><th>Відгук</th><th>Статус</th></tr>
        <?php while($r=$reviews->fetch_assoc()): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td><?= h($r['title']) ?></td>
            <td><?= h($r['username']) ?></td>
            <td><?= (int)$r['rating'] ?>/5</td>
            <td><?= h($r['body']) ?></td>
            <td><?= h($r['status']) ?></td>
          </tr>
        <?php endwhile; ?>
      </table>

    <?php elseif ($tab==='analytics'): ?>
      <?php
      $from = getv('from', date('Y-m-d', strtotime('-7 days')));
      $to   = getv('to', date('Y-m-d'));
      $stmt = $conn->prepare("
        SELECT event_type, DATE(created_at) d, COUNT(*) cnt
        FROM analytics_events
        WHERE DATE(created_at) BETWEEN ? AND ?
        GROUP BY event_type, DATE(created_at)
        ORDER BY d
      ");
      $stmt->bind_param('ss',$from,$to);
      $stmt->execute(); $res=$stmt->get_result();
      $agg=[]; while($r=$res->fetch_assoc()){ $agg[$r['d']][$r['event_type']] = (int)$r['cnt']; }
      $types=['page_view','view_book','add_to_cart','purchase'];
      ?>
      <form method="get" class="card" style="margin-bottom:16px;">
        <input type="hidden" name="tab" value="analytics">
        Від: <input type="date" name="from" value="<?= $from ?>">
        До: <input type="date" name="to" value="<?= $to ?>">
        <button class="btn">Показати</button>
      </form>
      <table class="orders-table">
        <tr><th>Дата</th><?php foreach($types as $t): ?><th><?= $t ?></th><?php endforeach; ?></tr>
        <?php foreach($agg as $d=>$row): ?>
          <tr>
            <td><?= $d ?></td>
            <?php foreach($types as $t): ?><td><?= $row[$t] ?? 0 ?></td><?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <p>Оберіть розділ зліва.</p>
    <?php endif; ?>
  </section>
>>>>>>> bb325550c9229aaa4ba21cf919905c8fa94a2afb
</div>

</body>
</html>

<<<<<<< HEAD
<?php
$conn->close();
?>
=======
<?php $conn->close(); ?>
>>>>>>> bb325550c9229aaa4ba21cf919905c8fa94a2afb
