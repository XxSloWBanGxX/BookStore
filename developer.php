<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8" />
  <title>Про розробника</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
.developer-container {
  max-width: 700px;
  margin: 40px auto;
  padding: 20px;
  background: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  font-family: Arial, sans-serif;
  text-align: center;
}
.developer-photo {
  width: 250px;
  height: 250px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 25px;
  border: 3px solid #444;
  display: inline-block;
}
.developer-name {
  font-size: 28px;
  font-weight: bold;
  margin-bottom: 15px;
  color: #222;
}
.developer-info {
  font-size: 16px;
  line-height: 1.6;
  color: #333;
  text-align: left;
  max-width: 600px;
  margin: 0 auto;
}
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="developer-container">
  <img src="img/your-photo.jpg" alt="Фото розробника" class="developer-photo" />
  <div class="developer-name">Мосейчук Богдан</div>
  <div class="developer-info">
    <p>Привіт! Я — Мосейчук Богдан, розробник цього сайту. Ідея створити онлайн-магазин книг виникла через мою любов до літератури та бажання зробити процес вибору та покупки книг зручним і приємним для користувачів.</p>
    <p>Цей проект реалізований за допомогою PHP, MySQL, HTML, CSS та JavaScript. Моя мета — створити простий і зрозумілий інтерфейс для любителів книг, де можна швидко знаходити цікаві видання і замовляти їх онлайн.</p>
    <p>Дякую, що відвідали мій сайт!</p>
  </div>
</div>

</body>
</html>
