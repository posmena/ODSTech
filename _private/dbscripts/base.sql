CREATE TABLE IF NOT EXISTS `pm_networks` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `last_update` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `class_name` (`class_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `pm_networks` (`id`, `name`, `last_update`, `class_name`) VALUES
(1, 'Affiliate Window', 0, 'affiliate_window'),
(2, 'Webgains', 1300725066, 'webgains');

--
-- Table structure for table `pm_programs`
--

CREATE TABLE IF NOT EXISTS `pm_programs` (
  `id` int(11) NOT NULL auto_increment,
  `network_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `merchant_ref` varchar(10) NOT NULL,
  `logo` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


--
-- Table structure for table `pm_feeds`
--

CREATE TABLE IF NOT EXISTS `pm_feeds` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` text NOT NULL,
  `url` text NOT NULL,
  `products` int(11) NOT NULL,
  `network_id` int(11) NOT NULL,
  `merchant_ref` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `network_id` (`network_id`,`merchant_ref`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


--
-- Table structure for table `pm_products`
--

CREATE TABLE IF NOT EXISTS `pm_products` (
  `id` int(11) NOT NULL auto_increment,
  `feed_id` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `ProductPrice` decimal(9,2) NOT NULL,
  `ProductDescription` text NOT NULL,
  `SummaryDescription` varchar(255) NOT NULL,
  `Gender` varchar(255) NOT NULL,
  `BrandName` varchar(255) NOT NULL,
  `RRP` varchar(255) NOT NULL,
  `ProductID` varchar(255) NOT NULL,
  `AffiliateURL` text NOT NULL,
  `ImageURL` varchar(255) NOT NULL,
  `Category` varchar(255) NOT NULL,
  `SmallImageURL` varchar(255) NOT NULL,
  `LargeImageURL` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `feed_id` (`feed_id`,`ProductID`),
  KEY `feed_id_2` (`feed_id`),
  FULLTEXT KEY `BrandName` (`BrandName`),
  FULLTEXT KEY `ProductName` (`ProductName`,`ProductDescription`,`BrandName`,`ProductID`,`Category`),
  FULLTEXT KEY `ProductID` (`ProductID`),
  FULLTEXT KEY `ProductName_2` (`ProductName`,`ProductDescription`,`SummaryDescription`,`BrandName`,`ProductID`,`AffiliateURL`,`Category`,`SmallImageURL`,`LargeImageURL`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;



--
-- Table structure for table `pm_categories`
--

CREATE TABLE IF NOT EXISTS `pm_categories` (
  `id` int(11) NOT NULL auto_increment,
  `feed_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `feed_id` (`feed_id`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
