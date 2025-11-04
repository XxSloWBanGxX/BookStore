-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Чрв 09 2025 р., 19:54
-- Версія сервера: 8.0.42
-- Версія PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `shop`
--

-- --------------------------------------------------------

--
-- Структура таблиці `books`
--

CREATE TABLE `books` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `created_at`, `image`, `price`) VALUES
(1, 'Тіні забутих предків', 'Михайло Коцюбинський', 'Роман про карпатських гуцулів, про їх життя, кохання і віру.', '2025-05-27 15:35:21', 'img/book1.jpg', 120.00),
(2, 'Кобзар', 'Тарас Шевченко', 'Збірка віршів, що стала символом української культури.', '2025-05-27 15:35:21', 'img/book2.jpg', 250.50),
(3, 'Сто років самотності', 'Габріель Гарсія Маркес', 'Магічний реалізм і сімейна сага в одному романі.', '2025-05-27 15:35:21', 'img/book3.jpg', 99.99),
(4, 'Гаррі Поттер і філософський камінь', 'Джоан Роулінг', 'Перша книга про пригоди Гаррі Поттера.', '2025-05-27 16:23:50', 'img/book4.jpg', 180.00),
(5, 'Володар перснів: Братство Персня', 'Джон Р.Р. Толкін', 'Епічна фентезі-сага про подорожі та боротьбу зі злом.', '2025-05-27 16:23:50', 'img/book5.jpg', 75.25),
(6, '1984', 'Джордж Орвелл', 'Антиутопічний роман про тоталітарне суспільство.', '2025-05-27 16:23:50', 'img/book6.jpg', 300.00),
(7, 'Майстер і Маргарита', 'Михайло Булгаков', 'Містична історія з елементами сатири і філософії.', '2025-05-27 16:23:50', 'img/book7.jpg', 45.00),
(8, 'Пікнік на узбіччі', 'Аркадій і Борис Стругацькі', 'Науково-фантастична повість про загадкову Зону.', '2025-05-27 16:23:50', 'img/book8.jpg', 150.75);

-- --------------------------------------------------------

--
-- Структура таблиці `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `status` varchar(50) NOT NULL DEFAULT 'Новий',
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(6, 'roman', '$2y$10$v91W6rb6XPe0a1C1e7UMe.iGHiBFDiqEuDusU/SJo0NRtsc2Ct.MW', 'user'),
(7, 'bagdan', '$2y$10$7hjm4ekFXHrhL07nHHsQ0u1mBnMhlNDH/BwyBM1m6dtuZgeq7J0Sa', 'user'),
(10, 'admin', '$2y$10$3Osfv5oZ0cmVc0k6JeXcUe94R2sobaAn1ouP2sJHhNId4bVV9KZJG', 'admin');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `books`
--
ALTER TABLE `books`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблиці `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
