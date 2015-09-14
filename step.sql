-- phpMyAdmin SQL Dump
-- version 4.0.10.6
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 08 2015 г., 18:18
-- Версия сервера: 5.5.41-log
-- Версия PHP: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `step`
--

-- --------------------------------------------------------

--
-- Структура таблицы `address`
--

CREATE TABLE IF NOT EXISTS `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `country` varchar(128) NOT NULL,
  `state` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `brand`
--

CREATE TABLE IF NOT EXISTS `brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `brand`
--

INSERT INTO `brand` (`id`, `name`, `active`) VALUES
(1, 'Brana', 1),
(2, 'Kort', 1),
(3, 'Gloriya Jeans', 1),
(4, 'Amanda Lot', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `category`
--

INSERT INTO `category` (`id`, `active`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `category_lang`
--

CREATE TABLE IF NOT EXISTS `category_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Дамп данных таблицы `category_lang`
--

INSERT INTO `category_lang` (`id`, `id_lang`, `id_object`, `name`) VALUES
(1, 1, 1, 'Платья'),
(2, 2, 1, 'Horum'),
(3, 3, 1, 'Horum'),
(4, 1, 2, 'Юбки'),
(5, 2, 2, 'Brana'),
(6, 3, 2, 'Brana'),
(7, 1, 3, 'Брюки'),
(8, 2, 3, 'Gloriya Jeans'),
(9, 3, 3, 'Gloriya Jeans'),
(10, 1, 4, 'Купальники'),
(11, 2, 4, 'Lenora'),
(12, 3, 4, 'Lenora'),
(13, 1, 5, 'Костюмы'),
(14, 2, 5, 'Kort'),
(15, 3, 5, 'Kort');

-- --------------------------------------------------------

--
-- Структура таблицы `lang`
--

CREATE TABLE IF NOT EXISTS `lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `lang`
--

INSERT INTO `lang` (`id`, `code`, `active`) VALUES
(1, 'ru', 1),
(2, 'ua', 1),
(3, 'us', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `lang_lang`
--

CREATE TABLE IF NOT EXISTS `lang_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `lang_lang`
--

INSERT INTO `lang_lang` (`id`, `id_lang`, `id_object`, `name`) VALUES
(1, 1, 1, 'Русский'),
(2, 2, 1, 'Російський'),
(3, 3, 1, 'Russian'),
(4, 1, 2, 'Украинский'),
(5, 2, 2, 'Український'),
(6, 3, 2, 'Ukranian'),
(7, 1, 3, 'Английский'),
(8, 2, 3, 'Англійський'),
(9, 3, 3, 'English');

-- --------------------------------------------------------

--
-- Структура таблицы `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_address` int(11) NOT NULL,
  `add` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `order_product`
--

CREATE TABLE IF NOT EXISTS `order_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_order` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) NOT NULL,
  `id_brand` int(11) NOT NULL,
  `price` float(6,2) NOT NULL,
  `articul` varchar(32) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `add` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Дамп данных таблицы `product`
--

INSERT INTO `product` (`id`, `id_category`, `id_brand`, `price`, `articul`, `active`, `add`) VALUES
(1, 1, 4, 330.00, '2133', 1, '2015-06-08 09:22:50'),
(2, 1, 2, 660.00, '2110', 1, '2015-06-08 09:23:27'),
(3, 1, 1, 600.00, '210', 1, '2015-06-08 09:23:33'),
(5, 1, 1, 600.00, '211', 1, '2015-06-08 09:23:54'),
(6, 1, 3, 680.00, '123', 1, '2015-06-08 09:24:40'),
(7, 2, 4, 200.00, '133', 1, '2015-06-08 09:27:02'),
(8, 2, 1, 300.00, '2333', 1, '2015-06-08 09:27:32'),
(9, 2, 1, 300.00, '345', 1, '2015-06-08 09:27:59'),
(10, 2, 3, 200.00, '4532', 1, '2015-06-08 09:28:27'),
(11, 2, 4, 120.00, '541', 1, '2015-06-08 09:28:56'),
(12, 3, 2, 200.00, '11111', 1, '2015-06-08 09:29:34'),
(13, 3, 1, 500.00, '25666', 1, '2015-06-08 09:29:59'),
(14, 3, 4, 290.00, '3221', 1, '2015-06-08 09:30:21');

-- --------------------------------------------------------

--
-- Структура таблицы `product_image`
--

CREATE TABLE IF NOT EXISTS `product_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_product` int(11) NOT NULL,
  `file` varchar(32) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Дамп данных таблицы `product_image`
--

INSERT INTO `product_image` (`id`, `id_product`, `file`, `position`) VALUES
(14, 14, '126b8758ca7766f460130912378bec65', 1),
(15, 14, '86bc2774af3181440629c0a16fcc0b87', 1),
(16, 13, '836c387ecb31497d575b0ba86e4800ff', 1),
(17, 13, '86d7268624b9d6f50848be37464a5474', 1),
(18, 12, '04c7a35a973a64e879a6e2d0591a7959', 1),
(19, 11, '779d28d9cff8674915ba31cc26fc25ab', 1),
(20, 10, '894e895c880a1fc8fc60fe8e08e356a2', 1),
(21, 9, '6bb83c34edb144649f405c24792cb811', 1),
(22, 9, '6f67a32d3a9c88e4eed373da2e8d3f63', 1),
(23, 8, '4eafc41e1bfaa2c743f22dd92c2f493d', 1),
(24, 8, 'a15f3d224a461de9d856545f1caf3978', 1),
(25, 7, '183c3ea1bdfa62f2e61b919bfa3c561d', 1),
(26, 6, '8564c16a3d7cd13fa1a0941efbe73bb3', 1),
(27, 5, '05eb2ce85e3a8b3792eb32f734e83d7f', 1),
(28, 3, '5c47a235b22a57668e65e41656e2210c', 1),
(29, 3, 'd6a6dc0db2e71a74db4e0984ea476859', 1),
(30, 2, 'b79301a1817e4b5c6fddcabaf7bfbe0e', 1),
(31, 1, '237232c84f3fcde486a5938247435521', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `product_lang`
--

CREATE TABLE IF NOT EXISTS `product_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_lang` int(11) NOT NULL,
  `id_object` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

--
-- Дамп данных таблицы `product_lang`
--

INSERT INTO `product_lang` (`id`, `id_lang`, `id_object`, `name`, `description`) VALUES
(1, 1, 1, 'Платье летнее', ''),
(2, 2, 1, 'Платье летнее', ''),
(3, 3, 1, 'Платье летнее', ''),
(4, 1, 2, 'Платье 2110', ''),
(5, 2, 2, 'Платье 211', ''),
(6, 3, 2, 'Платье 211', ''),
(7, 1, 3, 'Платье 210', ''),
(8, 2, 3, 'Платье 211', ''),
(9, 3, 3, 'Платье 211', ''),
(13, 1, 5, 'Платье 211', ''),
(14, 2, 5, 'Платье 211', ''),
(15, 3, 5, 'Платье 211', ''),
(16, 1, 6, 'Платье 123', ''),
(17, 2, 6, 'Платье 123', ''),
(18, 3, 6, 'Платье 123', ''),
(19, 1, 7, 'Юбка 1', ''),
(20, 2, 7, 'Юбка 1', ''),
(21, 3, 7, 'Юбка 1', ''),
(22, 1, 8, 'Юбка 2', ''),
(23, 2, 8, 'Юбка 2', ''),
(24, 3, 8, 'Юбка 2', ''),
(25, 1, 9, 'Юбка 3', ''),
(26, 2, 9, 'Юбка 3', ''),
(27, 3, 9, 'Юбка 3', ''),
(28, 1, 10, 'Юбка 4', ''),
(29, 2, 10, 'Юбка 4', ''),
(30, 3, 10, 'Юбка 4', ''),
(31, 1, 11, 'Юбка 5', ''),
(32, 2, 11, 'Юбка 5', ''),
(33, 3, 11, 'Юбка 5', ''),
(34, 1, 12, 'Брюки 1', ''),
(35, 2, 12, 'Брюки 1', ''),
(36, 3, 12, 'Брюки 1', ''),
(37, 1, 13, 'Брюки 2', ''),
(38, 2, 13, 'Брюки 2', ''),
(39, 3, 13, 'Брюки 2', ''),
(40, 1, 14, 'Брюки 3', ''),
(41, 2, 14, 'Брюки 3', ''),
(42, 3, 14, 'Брюки 3', '');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `password` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  `add` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `add`) VALUES
(1, 'user1@site.com', '24c9e15e52afc47c225b757e7bee1f9d', 'user1', '2015-06-08 18:16:42');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
