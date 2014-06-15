CREATE TABLE `KuwasysClassteachers` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `forename` varchar(64) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `address` varchar(255) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `telephone` varchar(64) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `email` varchar(64) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci