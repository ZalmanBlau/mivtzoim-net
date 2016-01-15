-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 05, 2016 at 02:29 PM
-- Server version: 5.5.31-MariaDB-cll-lve
-- PHP Version: 5.3.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mivtzoim_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `res_master`
--

CREATE TABLE IF NOT EXISTS `res_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assigned` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=>not assigned, 1=>assigned to chabad (see res_assigned.master_id to reverse lookup)',
  `jewishness` int(11) NOT NULL,
  `name` text NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `address` text NOT NULL,
  `apt_num` text NOT NULL,
  `city` text NOT NULL,
  `zip` int(11) NOT NULL,
  `state` text NOT NULL,
  `value` text NOT NULL,
  `primary_res` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Primary Resident (0=>no, 1=>yes)',
  `age_id` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'id in ages table for this record',
  `lat_lng` text NOT NULL,
  `updated_date` text NOT NULL,
  `updated_time` int(11) NOT NULL,
  `moved_in_time` int(11) NOT NULL,
  `moved_in_date` text NOT NULL,
  `new_mover` tinyint(4) NOT NULL DEFAULT '0',
  `source` text NOT NULL,
  `source_id` int(11) NOT NULL COMMENT 'ID of source in source table',
  `source_val` text NOT NULL COMMENT 'Value to lookup this record in source',
  `source2_id` tinyint(4) NOT NULL,
  `source2_val` int(11) NOT NULL,
  `cron_geo_update_status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zip` (`zip`),
  KEY `assigned` (`assigned`,`jewishness`,`zip`),
  KEY `assigned_2` (`assigned`,`zip`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=265174 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

