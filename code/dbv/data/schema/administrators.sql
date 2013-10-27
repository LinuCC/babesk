CREATE TABLE `administrators` (
  `ID` smallint(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `password` varchar(32) COLLATE latin1_german2_ci NOT NULL,
  `GID` smallint(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci