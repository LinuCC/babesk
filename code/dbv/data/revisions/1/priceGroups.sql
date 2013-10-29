CREATE TABLE `priceGroups` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `max_credit` decimal(4,2) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci