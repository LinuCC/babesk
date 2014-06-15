CREATE TABLE `BabeskPriceClasses` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `GID` smallint(5) NOT NULL,
  `price` decimal(6,2) NOT NULL,
  `pc_ID` smallint(5) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci