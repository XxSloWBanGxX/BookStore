<?php
session_start();  // Запускаємо сесію на початку, до будь-якого виводу

// Параметри підключення до бази
$host = 'localhost';
$user = 'root';
<<<<<<< HEAD
$password = '';
=======
$password = 'smaik1322';
>>>>>>> bb325550c9229aaa4ba21cf919905c8fa94a2afb
$dbname = 'shop';

// Підключення до бази
$conn = new mysqli($host, $user, $password, $dbname);

// Перевірка підключення
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

// Отримання книг з бази
$sql_slider = "SELECT title, author, description, image FROM books LIMIT 6";
$result_slider = $conn->query($sql_slider);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Магазин книг</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
</head>
<body>

<?php include 'header.php'; ?>

<main>

<section class="section">
  <div class="container">
    <h2>Популярні книги</h2>
    <div class="swiper">
      <div class="swiper-wrapper">
        <?php if ($result_slider && $result_slider->num_rows > 0): ?>
          <?php while($row = $result_slider->fetch_assoc()): ?>
            <div class="swiper-slide book">
              <img src="<?= htmlspecialchars($row["image"]) ?>" alt="Обкладинка книги" class="book-cover">
              <h3><?= htmlspecialchars($row["title"]) ?></h3>
              <p><strong>Автор:</strong> <?= htmlspecialchars($row["author"]) ?></p>
              <p><?= htmlspecialchars(mb_strimwidth($row["description"], 0, 100, '...')) ?></p>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>Книг поки що немає.</p>
        <?php endif; ?>
      </div>
      <!-- Кнопки навігації -->
      <div class="swiper-button-prev">
        <img src="img/arrow-left.png" alt="Попередня">
      </div>
      <div class="swiper-button-next">
        <img src="img/arrow-right.png" alt="Наступна">
      </div>
      <!-- Пагінація -->
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>

<section id="about" class="section about-section">
    <div class="container">
        <h2>Про наш магазин</h2>
        <div class="about-text">
            <p> Вітаємо у «Книжковому Світі» — вашому надійному онлайн-магазині книг! Ми створили це місце для тих, хто цінує якісну літературу, бажає знайти нові улюблені книги і зануритися у захоплюючі світи різних жанрів.</p>
            <p> Наш асортимент включає широкий вибір творів: класичну і сучасну художню літературу, науково-популярні книги, фентезі, детективи, біографії, дитячу літературу та багато іншого. Ми співпрацюємо з найкращими видавництвами, тому ви можете бути впевнені в якості кожного примірника.</p>
            <p> Окрім багатого вибору, ми прагнемо зробити процес покупки максимально зручним і приємним. Інтуїтивний інтерфейс сайту допоможе швидко знайти потрібну книгу, а різноманітні способи оплати і доставки – отримати замовлення без зайвих клопотів.</p>
            <p> Наші постійні клієнти отримують спеціальні пропозиції, акції та знижки. Ми цінуємо вашу довіру і прагнемо зробити кожну покупку унікальною подією, що надихає на нові відкриття та знання.</p>
            <p> Запрошуємо вас приєднатися до нашої книжкової спільноти, слідкувати за новинками і відгуками, а також ділитися враженнями. Книжковий Світ – це не просто магазин, це простір для розвитку, натхнення і любові до читання.</p>
            <p>Дякуємо, що обираєте нас!</p>
        </div>
    </div>
</section>

</main>

<footer id="contact" class="site-footer">
  <div class="footer-container container">
    <div class="footer-column">
      <h3>Книжковий Світ</h3>
      <p>Ваш надійний онлайн-магазин книг. Натхнення, знання, відкриття — щодня з нами!</p>
    </div>
    <div class="footer-column">
      <h4>Навігація</h4>
      <ul>
        <li><a href="#home">Головна</a></li>
        <li><a href="#catalog">Каталог</a></li>
        <li><a href="#about">Про нас</a></li>
      </ul>
    </div>
    <div class="footer-column">
      <h4>Контакти</h4>
      <p>Email: info@bookstore.com</p>
      <p>Тел: +38 (098) 123-45-67</p>
      <div class="social-icons">
        <a href="#"><img src="img/facebook.png" alt="Facebook"></a>
        <a href="#"><img src="img/instagram.png" alt="Instagram"></a>
        <a href="#"><img src="img/telegram.png" alt="Telegram"></a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; <?= date("Y") ?> Книжковий Світ. Всі права захищені.</p>
  </div>
</footer>

<!-- Скрипти -->
<script src="js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
  const swiper = new Swiper('.swiper', {
    slidesPerView: 5,
    spaceBetween: 30,
    loop: true,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
        spaceBetween: 10,
      },
      640: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      1024: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
    },
  });
</script>

</body>
</html>

<?php
$conn->close();
?>
