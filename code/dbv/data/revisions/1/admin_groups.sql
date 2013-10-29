CREATE TABLE `admin_groups` (
  `ID` smallint(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `modules` varchar(1024) COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci