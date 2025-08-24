-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 13 2024 г., 16:13
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `workspace`
--

-- --------------------------------------------------------

--
-- Структура таблицы `config`
--

CREATE TABLE `config` (
  `param` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `config`
--

INSERT INTO `config` (`param`, `value`) VALUES
('TELEGRAM_NOTIFICATIONS', 'disabled'),
('WORKSPACE_MESSAGE_PREFIX', '[Workspace]'),
('CLIENT_APP_FEAUTERS', 'disabled'),
('CLIENT_APP_ADDRESS', '-'),
('TERMINAL_FEAUTERS', 'enabled'),
('TV_FEAUTERS', 'disabled'),
('SERVICE_TOKEN', 'sAStPjnfDAO8cUbP'),
('SHIFT_OPEN_REPORTS', 'disabled'),
('SHIFT_CLOSE_REPORTS', 'enabled'),
('DAILY_REPORTS', 'enabled'),
('WEEKLY_REPORTS', 'enabled'),
('MONTHLY_REPORTS', 'disabled');

-- --------------------------------------------------------

--
-- Структура таблицы `market_groups`
--

CREATE TABLE `market_groups` (
  `id` int NOT NULL,
  `supergroup_id` int NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `market_groups`
--

INSERT INTO `market_groups` (`id`, `supergroup_id`, `name`) VALUES
(1, 1, 'Энергетики'),
(2, 1, 'Кофе'),
(3, 1, 'Газировка'),
(4, 2, 'Чипсы'),
(5, 2, 'Сладкое'),
(6, 3, 'Лимонады'),
(7, 3, 'Классическая'),
(8, 3, 'Мохито');

-- --------------------------------------------------------

--
-- Структура таблицы `market_invoices`
--

CREATE TABLE `market_invoices` (
  `id` int NOT NULL,
  `created_by` int NOT NULL COMMENT 'staff id сотрудника, создавшего документ',
  `type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'entrance или cancellation',
  `price` int NOT NULL,
  `products_list` json NOT NULL,
  `creation_date` datetime NOT NULL,
  `status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'created, accepted или deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `market_invoices`
--

INSERT INTO `market_invoices` (`id`, `created_by`, `type`, `price`, `products_list`, `creation_date`, `status`) VALUES
(1, 2, 'entrance', 1000, '[{\"value\": 20, \"product_id\": 2}, {\"value\": 10, \"product_id\": 3}, {\"value\": 10, \"product_id\": 1}]', '2024-04-09 16:50:03', 'accepted');

-- --------------------------------------------------------

--
-- Структура таблицы `market_log`
--

CREATE TABLE `market_log` (
  `id` int NOT NULL,
  `staff_id` int NOT NULL,
  `type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'sale, correct, invoice_acceptance, invoice_delete',
  `data` json NOT NULL COMMENT 'подробности операции в json',
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `market_log`
--

INSERT INTO `market_log` (`id`, `staff_id`, `type`, `data`, `date`) VALUES
(1, 2, 'correct', '{\"product_id\": 1, \"new_quantity\": \"10\", \"old_quantity\": \"0\", \"operation_type\": \"correct\"}', '2024-04-09 16:47:42'),
(2, 2, 'invoice_acceptance', '{\"invoice_id\": \"1\"}', '2024-04-13 14:20:36');

-- --------------------------------------------------------

--
-- Структура таблицы `market_products`
--

CREATE TABLE `market_products` (
  `id` int NOT NULL,
  `group_id` int NOT NULL,
  `name` text NOT NULL,
  `quantity` int NOT NULL,
  `coast_sheme` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `market_products`
--

INSERT INTO `market_products` (`id`, `group_id`, `name`, `quantity`, `coast_sheme`) VALUES
(1, 1, 'Адреналин раш 0.5', 20, '[{\"value\": \"150\", \"currency_id\": \"1\"}, {\"value\": \"150\", \"currency_id\": \"2\"}]'),
(2, 1, 'Монстер пацифик панч', 20, '[{\"value\": \"220\", \"currency_id\": \"1\"}, {\"value\": \"220\", \"currency_id\": \"2\"}]'),
(3, 1, 'Берн яблоко-киви', 10, '[{\"value\": \"170\", \"currency_id\": \"1\"}, {\"value\": \"170\", \"currency_id\": \"2\"}]');

-- --------------------------------------------------------

--
-- Структура таблицы `market_supergroups`
--

CREATE TABLE `market_supergroups` (
  `id` int NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `market_supergroups`
--

INSERT INTO `market_supergroups` (`id`, `name`) VALUES
(1, 'Напитки'),
(2, 'Снеки'),
(3, 'Горячее питание');

-- --------------------------------------------------------

--
-- Структура таблицы `staff`
--

CREATE TABLE `staff` (
  `id` int NOT NULL,
  `token` text NOT NULL,
  `auth` text NOT NULL,
  `staffgroup_id` int NOT NULL,
  `name` text NOT NULL,
  `second_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `staff`
--

INSERT INTO `staff` (`id`, `token`, `auth`, `staffgroup_id`, `name`, `second_name`) VALUES
(2, '63f6547e9f343d6e6c', '{\"username\":\"Liberty\",\"password\":\"12345123\"}', 1, '-', '-'),
(7, '4cef894e26857af682', '{\"username\":\"eliseevkd\",\"password\":\"12345\"}', 2, 'Кузьма', 'Елисеев'),
(8, '31f6be829a91dce262', '{\"username\":\"Yana006\",\"password\":\"12345123\"}', 3, 'Яна', 'Захарова');

-- --------------------------------------------------------

--
-- Структура таблицы `staffgroups`
--

CREATE TABLE `staffgroups` (
  `id` int NOT NULL,
  `name` text NOT NULL,
  `rights` text NOT NULL COMMENT 'JSON - описание прав для группы'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `staffgroups`
--

INSERT INTO `staffgroups` (`id`, `name`, `rights`) VALUES
(1, 'Разработчик', '{\"access_flags\":{\"users_get\":true,\"users_edit\":true,\"payments\":true,\"config\":true,\"staff\":true,\"staffgroups\":true,\"hosts_controls\":true,\"hosts_edit\":true,\"market_main\":true,\"market_edit\":true,\"apps\":true},\"web\":{\"access\":true,\"tabs_flags\":{\"hosts\":true,\"sales\":false,\"market\":true,\"staff\":true,\"apps\":false,\"config\":true,\"users\":true}},\"mobile\":{\"access\":true}}'),
(2, 'Владелец', '{\"access_flags\":{\"users_get\":true,\"users_edit\":true,\"payments\":true,\"config\":true,\"staff\":true,\"staffgroups\":true,\"hosts_controls\":true,\"hosts_edit\":true,\"market_main\":true,\"market_edit\":true,\"apps\":true},\"web\":{\"access\":true,\"tabs_flags\":{\"hosts\":true,\"sales\":false,\"market\":false,\"staff\":true,\"apps\":true,\"config\":false}},\"mobile\":{\"access\":true}}'),
(3, 'Админ', '{\"access_flags\":{\"users_get\":true,\"users_edit\":false,\"payments\":true,\"config\":false,\"staff\":false,\"staffgroups\":false,\"hosts_controls\":true,\"hosts_edit\":false,\"market_main\":true,\"market_edit\":false,\"apps\":false},\"web\":{\"access\":false,\"tabs_flags\":{\"hosts\":false,\"sales\":false,\"market\":false,\"staff\":false,\"apps\":false,\"config\":false}},\"mobile\":{\"access\":true}}'),
(4, 'Управляющий', '{\"access_flags\":{\"users_get\":true,\"users_edit\":true,\"payments\":true,\"config\":false,\"staff\":true,\"staffgroups\":false,\"hosts_controls\":true,\"hosts_edit\":false,\"market_main\":true,\"market_edit\":true,\"apps\":false},\"web\":{\"access\":true,\"tabs_flags\":{\"hosts\":true,\"sales\":true,\"market\":true,\"staff\":true,\"apps\":false,\"config\":false}},\"mobile\":{\"access\":false}}');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `market_groups`
--
ALTER TABLE `market_groups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `market_invoices`
--
ALTER TABLE `market_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `market_log`
--
ALTER TABLE `market_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `market_products`
--
ALTER TABLE `market_products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `market_supergroups`
--
ALTER TABLE `market_supergroups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `staffgroups`
--
ALTER TABLE `staffgroups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `market_groups`
--
ALTER TABLE `market_groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `market_invoices`
--
ALTER TABLE `market_invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `market_log`
--
ALTER TABLE `market_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `market_products`
--
ALTER TABLE `market_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `market_supergroups`
--
ALTER TABLE `market_supergroups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `staffgroups`
--
ALTER TABLE `staffgroups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
