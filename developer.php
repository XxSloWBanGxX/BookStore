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

  <title>Розробники</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
.page-container {
  max-width: 1000px;
  margin: 60px auto;
  padding: 20px;
  font-family: 'Segoe UI', sans-serif;
  text-align: center;
}
.page-container h1 {
  font-size: 36px;
  margin-bottom: 40px;
  color: #222;
}
.developers-grid {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 40px;
}
.developer-card {
  width: 420px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  padding: 25px;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.developer-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0 20px rgba(0,0,0,0.15);
}
.developer-photo {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 20px;
  border: 3px solid #444;
}
.developer-name {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 10px;
  color: #1a1a1a;
}
.developer-role {
  font-size: 18px;
  color: #555;
  margin-bottom: 15px;
}
.developer-info {
  font-size: 15px;
  line-height: 1.6;
  color: #333;
  text-align: justify;
}
@media (max-width: 768px) {
  .developer-card {
    width: 90%;
  }
}
  </style>

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


<div class="page-container">
  <h1>Наші розробники</h1>
  <div class="developers-grid">

    <!-- Богдан -->
    <div class="developer-card">
      <img src="img/your-photo.jpg" alt="Мосейчук Богдан" class="developer-photo" />
      <div class="developer-name">Мосейчук Богдан</div>
      <div class="developer-role">Full-stack розробник</div>
      <div class="developer-info">
        <p>Привіт! Я — Мосейчук Богдан, розробник цього сайту. Моє завдання — забезпечити стабільну роботу серверної частини, бази даних і логіки магазину.</p>
        <p>Працюю з PHP, HTML, CSS, JavaScript, MySQL і створюю системи, які роблять ваш досвід зручним і безпечним. Мені подобається оптимізувати код і робити все «під капотом» швидше.</p>
      </div>
    </div>

    <!-- Роман -->
    <div class="developer-card">
      <img src="img/developer.jpg" alt="Симчич Роман" class="developer-photo" />
      <div class="developer-name">Симчич Роман</div>
      <div class="developer-role">Full-stack розробник</div>
      <div class="developer-info">
        <p>Вітаю! Я — Симчич Роман, автор і дизайнер цього проєкту. Відповідаю за інтерфейс, структуру сайту та зручність користувачів.</p>
        <p>У роботі використовую PHP, HTML, CSS, JavaScript і MySQL. Люблю створювати елегантні, сучасні рішення, що поєднують дизайн і функціональність.</p>
      </div>
    </div>



</div>

</body>
</html>
