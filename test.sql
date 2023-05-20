SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `secret` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `description` varchar(256) NOT NULL,
  `last_failed_login_attempt_date` datetime DEFAULT NULL,
  `failed_login_attempt_count` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `email`, `username`, `secret`, `password`, `description`, `last_failed_login_attempt_date`, `failed_login_attempt_count`) VALUES
(1, 'test@test.com', 'test', '67469527a6e4c3d6582d6836d06fdaf9', '098f6bcd4621d373cade4e832627b4f6', 'test description', '2023-05-16 13:15:24', 0),
(2, 'test1@test.com', 'test1', 'ff870a501275fa042d814d30b748888f', '098f6bcd4621d373cade4e832627b4f6', 'test1 description', '2023-05-17 08:46:16', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
