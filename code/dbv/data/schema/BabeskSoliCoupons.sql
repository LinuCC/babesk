CREATE TABLE `BabeskSoliCoupons` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8