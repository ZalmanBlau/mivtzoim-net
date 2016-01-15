SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


-- Table structure for table `us_census_places`
--

CREATE TABLE IF NOT EXISTS `us_places` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(75) DEFAULT '0',
  `state` varchar(25) DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT row_integrity UNIQUE (`city`, `state`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=265174 ;

CREATE INDEX `city`
ON `us_places` (`city`(25));

CREATE INDEX `state`
ON `us_places` (`state`(15));