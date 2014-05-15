CREATE TABLE `KuwasysClassCategories` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE latin1_german2_ci NOT NULL,
  `translatedName` varchar(255) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci