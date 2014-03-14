CREATE TABLE `class` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `description` varchar(1024) COLLATE latin1_german2_ci NOT NULL,
  `maxRegistration` int(5) NOT NULL,
  `registrationEnabled` tinyint(1) NOT NULL,
  `unitId` int(11) unsigned NOT NULL,
  `schoolyearId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci