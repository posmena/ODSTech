-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 04, 2011 at 09:16 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `odstech`
--

-- --------------------------------------------------------

--
-- Table structure for table `ot_images`
--

DROP TABLE IF EXISTS `ot_images`;
CREATE TABLE `ot_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL DEFAULT 'A Caption...',
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ot_images`
--


-- --------------------------------------------------------

--
-- Table structure for table `ot_users`
--

DROP TABLE IF EXISTS `ot_users`;
CREATE TABLE `ot_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `sname` varchar(255) NOT NULL,
  `lastlogin` varchar(255) NOT NULL,
  `userclass` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ot_users`
--

INSERT INTO `ot_users` VALUES(1, 'bdeveaux', '0b41c92c6e3d80f7adc36cd239ca9778', 'bobby@odst.co.uk', 'Bobby', 'DeVeaux', '', 'adminuser');
INSERT INTO `ot_users` VALUES(2, 'rufus', 'a0f62d1a9d3f65bd217e69a41fe550b0', 'rufus@odst.co.uk', 'Rufus', 'Bazley', '', 'adminuser');