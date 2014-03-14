CREATE TABLE `BabeskPriceClasses` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `GID` smallint(5) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `pc_ID` smallint(5) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8