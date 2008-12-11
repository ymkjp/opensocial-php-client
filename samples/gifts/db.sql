DROP TABLE IF EXISTS `socialnuts`;
CREATE TABLE `socialnuts` (
  `from` varchar(50) NOT NULL default '0',
  `to` varchar(50) NOT NULL default '0',
  `nut` varchar(20) NOT NULL default '0',
  `ts` int(11) NOT NULL default '0',
  `comments` varchar(255) NOT NULL default '0',
  KEY `from` (`from`),
  KEY `to` (`to`)
)

