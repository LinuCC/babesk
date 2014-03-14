CREATE TABLE `SystemGroups` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ixLft` (`lft`),
  KEY `ixRgt` (`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8