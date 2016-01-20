SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- Table structure for table `users_mivtzoim_data`
--

CREATE TABLE IF NOT EXISTS `users_mivtzoim_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `res_master_tmp_id` int(11) NOT NULL,
  `users_id` int(11) unsigned NOT NULL,
  `new_data?` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (res_master_tmp_id) REFERENCES res_master_tmp(id),
  FOREIGN KEY (users_id) REFERENCES users(id),
  CONSTRAINT row_integrity UNIQUE(`users_id`, `res_master_tmp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=265174;

CREATE INDEX `users_id`
ON `users_mivtzoim_data` (`users_id`);