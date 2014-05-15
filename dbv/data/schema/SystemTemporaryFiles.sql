CREATE TABLE `SystemTemporaryFiles` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `created` datetime NOT NULL,
  `until` datetime NOT NULL,
  `usage` varchar(64) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci