SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- Table structure for table `mivtzoim_users`
--

CREATE TABLE IF NOT EXISTS `mivtzoim_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chabad_name` varchar(255),
  `first_name` varchar(255),
  `last_name` varchar(255),
  `limit`int(11),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=265174;