CREATE TABLE `classTeacher` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE latin1_german2_ci NOT NULL,
  `forename` varchar(64) COLLATE latin1_german2_ci NOT NULL,
  `address` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `telephone` varchar(64) COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci