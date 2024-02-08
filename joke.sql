-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 17 2023 г., 23:39
-- Версия сервера: 8.0.30
-- Версия PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `joke`
--

-- --------------------------------------------------------

--
-- Структура таблицы `joke`
--

CREATE TABLE `joke` (
  `id` int NOT NULL,
  `joke` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `joke_date` date DEFAULT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `joke`
--

INSERT INTO `joke` (`id`, `joke`, `joke_date`, `user`) VALUES
(108, 'анекдот 31werwerwerwerwer', '2023-08-07', 'admin'),
(109, 'анекдот 30', '2023-08-07', 'admin'),
(110, 'анекдот 29', '2023-08-07', 'admin'),
(111, 'анекдот 28', '2023-08-07', 'admin'),
(112, 'анекдот 27', '2023-08-07', 'admin'),
(113, 'анекдот 26', '2023-08-07', 'admin'),
(114, 'анекдот 25', '2023-08-07', 'admin'),
(115, 'анекдот 24', '2023-08-07', 'admin'),
(116, 'анекдот 23', '2023-08-07', 'admin'),
(117, 'анекдот 22', '2023-08-07', 'admin'),
(118, 'анекдот 21', '2023-08-07', 'admin'),
(119, 'анекдот 20', '2023-08-13', 'admin'),
(120, 'анекдот 19', '2023-08-13', 'admin'),
(121, 'анекдот 18', '2023-08-13', 'admin'),
(122, 'анекдот 17', '2023-08-13', 'admin'),
(123, 'анекдот 16', '2023-08-13', 'admin'),
(124, 'анекдот 15', '2023-08-13', 'admin'),
(125, 'анекдот 14', '2023-08-13', 'admin'),
(126, 'анекдот 13', '2023-08-13', 'admin'),
(127, 'анекдот 12', '2023-08-13', 'admin'),
(128, 'анекдот 11', '2023-08-13', 'admin'),
(129, 'анекдот 10', '2023-08-13', 'admin'),
(130, 'm875u867nj678m678m67', '2023-08-14', 'admin'),
(131, 'ynutyuntyutu', '2023-08-14', 'admin'),
(132, 'анекдот 9', '2023-08-14', 'admin'),
(133, 'анекдот8', '2023-08-14', 'admin'),
(134, 'анекдот7', '2023-08-14', 'admin'),
(135, 'tv5y5by54yb45yb54yb', '2023-08-14', 'admin'),
(136, 'анекдот6', '2023-08-14', 'admin'),
(137, 'укеукеукеукеук', '2023-08-14', 'admin'),
(138, 'rehryhryhryt', '2023-08-14', 'admin'),
(139, 'rthyrtyhrty', '2023-08-14', 'admin'),
(141, 'еинкеникенике', '2023-08-14', 'admin'),
(142, 'анекдот 4', '2023-08-14', 'admin'),
(143, 'анекдот 3', '2023-08-14', 'admin'),
(144, 'анекдот 2', '2023-08-14', 'admin'),
(145, 'анекдот 1 ', '2023-08-14', 'admin'),
(146, 'пвапвапвап', '2023-08-14', 'admin'),
(151, 'yunyunyutnyuntyun', '2023-08-14', 'admin'),
(152, '56n4676n4', '2023-08-14', 'admin'),
(156, '34b65y6b54y6b', '2023-08-14', 'admin'),
(158, 'weqfwerfwerf', '2023-08-17', 'admin'),
(159, 'werfwerfwe', '2023-08-17', 'admin'),
(160, 'sss', '2023-08-17', 'admin');

-- --------------------------------------------------------

--
-- Структура таблицы `joke_tag`
--

CREATE TABLE `joke_tag` (
  `id` int NOT NULL,
  `id_joke` int NOT NULL,
  `id_tag` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `joke_tag`
--

INSERT INTO `joke_tag` (`id`, `id_joke`, `id_tag`) VALUES
(188, 110, 1),
(189, 110, 15),
(227, 120, 1),
(228, 120, 13),
(249, 127, 1),
(259, 134, 1),
(260, 134, 16),
(261, 134, 19),
(265, 138, 13),
(360, 142, 1),
(361, 142, 16),
(362, 142, 19),
(363, 142, 21),
(368, 126, 1),
(370, 126, 19),
(372, 109, 1),
(373, 109, 16),
(374, 109, 20),
(375, 109, 27),
(393, 108, 1),
(394, 108, 15),
(397, 111, 1),
(399, 111, 21),
(408, 121, 1),
(409, 121, 13),
(410, 121, 15),
(411, 122, 1),
(413, 122, 20),
(414, 122, 29),
(419, 114, 1),
(421, 114, 21),
(422, 124, 1),
(423, 124, 13),
(424, 124, 15),
(426, 125, 1),
(427, 125, 15),
(428, 125, 20),
(429, 125, 24),
(430, 125, 31),
(431, 130, 1),
(433, 130, 21),
(434, 128, 1),
(435, 128, 24),
(436, 128, 32),
(437, 115, 1),
(438, 115, 13),
(440, 113, 1),
(441, 113, 15),
(442, 113, 19),
(446, 118, 1),
(447, 118, 15),
(448, 118, 20),
(452, 146, 1),
(453, 146, 21),
(454, 146, 27),
(455, 116, 1),
(456, 116, 12),
(457, 112, 1),
(458, 112, 12),
(459, 119, 1),
(460, 119, 12),
(466, 143, 1),
(467, 143, 12),
(468, 145, 12),
(469, 145, 16),
(471, 144, 1),
(472, 144, 15),
(474, 141, 1),
(475, 141, 12),
(476, 141, 15),
(477, 141, 20),
(478, 137, 1),
(479, 137, 12),
(480, 137, 15),
(481, 151, 1),
(482, 151, 16),
(484, 156, 1),
(485, 156, 12),
(486, 156, 13),
(487, 139, 1),
(488, 139, 12),
(489, 139, 13),
(494, 131, 1),
(495, 132, 1),
(496, 133, 1),
(497, 133, 31),
(498, 135, 1),
(507, 136, 1),
(508, 136, 13),
(509, 136, 15),
(511, 123, 1),
(512, 129, 1),
(513, 117, 1),
(514, 152, 1),
(516, 158, 15),
(517, 159, 15),
(523, 160, 12);

-- --------------------------------------------------------

--
-- Структура таблицы `tag`
--

CREATE TABLE `tag` (
  `id` int NOT NULL,
  `tag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `tag`
--

INSERT INTO `tag` (`id`, `tag`) VALUES
(1, 'секс'),
(12, 'Работа'),
(13, 'Отношение'),
(15, 'Родственники'),
(16, 'IT'),
(19, 'Черный юмор'),
(20, 'Кино'),
(21, 'Алкоголь'),
(22, 'Исторические'),
(24, 'Учеба'),
(27, 'Политика'),
(29, 'США'),
(31, 'Украина'),
(32, 'СССР');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `user`, `password`) VALUES
(1, 'admin', '123'),
(2, 'Lion', '123');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `joke`
--
ALTER TABLE `joke`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `joke_tag`
--
ALTER TABLE `joke_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_joke` (`id_joke`),
  ADD KEY `id_teg` (`id_tag`);

--
-- Индексы таблицы `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `joke`
--
ALTER TABLE `joke`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT для таблицы `joke_tag`
--
ALTER TABLE `joke_tag`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=524;

--
-- AUTO_INCREMENT для таблицы `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `joke_tag`
--
ALTER TABLE `joke_tag`
  ADD CONSTRAINT `joke_tag_ibfk_1` FOREIGN KEY (`id_joke`) REFERENCES `joke` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `joke_tag_ibfk_2` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
