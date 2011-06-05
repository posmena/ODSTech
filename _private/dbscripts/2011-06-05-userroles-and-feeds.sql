-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 05, 2011 at 07:31 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `odstech`
--

-- --------------------------------------------------------

--
-- Table structure for table `ot_clients`
--

DROP TABLE IF EXISTS `ot_clients`;
CREATE TABLE `ot_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ot_clients`
--

INSERT INTO `ot_clients` VALUES(1, 'EasyJet Holidays');

-- --------------------------------------------------------

--
-- Table structure for table `ot_client_users`
--

DROP TABLE IF EXISTS `ot_client_users`;
CREATE TABLE `ot_client_users` (
  `userid` int(11) NOT NULL,
  `clientid` int(11) NOT NULL,
  UNIQUE KEY `userid` (`userid`,`clientid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ot_client_users`
--

INSERT INTO `ot_client_users` VALUES(3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ot_feeds`
--

DROP TABLE IF EXISTS `ot_feeds`;
CREATE TABLE `ot_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `client_id` int(11) NOT NULL,
  `products` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ot_feeds`
--

INSERT INTO `ot_feeds` VALUES(1, 'Packages', 1, 0);
