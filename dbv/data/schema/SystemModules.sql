CREATE TABLE `SystemModules` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `executablePath` varchar(255) NOT NULL DEFAULT '',
  `displayInMenu` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `ixLft` (`lft`),
  KEY `ixRgt` (`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8