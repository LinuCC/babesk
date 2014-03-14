CREATE TABLE `jointClassInSchoolYear` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ClassID` int(11) unsigned NOT NULL,
  `SchoolYearID` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ClassID` (`ClassID`),
  KEY `SchoolYearID` (`SchoolYearID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci