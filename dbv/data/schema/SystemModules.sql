CREATE TABLE `SystemModules` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `executablePath` varchar(255) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  `displayInMenu` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci