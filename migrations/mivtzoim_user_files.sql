SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- Table structure for table `mivtzoim_user_files`
--

CREATE TABLE IF NOT EXISTS `mivtzoim_user_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_path` varchar(255) NOT NULL,
  `mivtzoim_user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT row_integrity UNIQUE(`mivtzoim_user_id`, `file_path`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=265174;