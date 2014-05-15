CREATE TABLE `UserGradesRepair` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE latin1_german2_ci NOT NULL,
  `grade` varchar(5) COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci