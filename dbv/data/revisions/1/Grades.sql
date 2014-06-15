CREATE TABLE `Grades` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `gradelevel` int(3) DEFAULT NULL,
  `schooltypeId` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci