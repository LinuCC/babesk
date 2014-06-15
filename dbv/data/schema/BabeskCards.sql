CREATE TABLE `BabeskCards` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cardnumber` varchar(10) COLLATE latin1_german2_ci NOT NULL,
  `UID` bigint(11) unsigned NOT NULL,
  `changed_cardID` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci