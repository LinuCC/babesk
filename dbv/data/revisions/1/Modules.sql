CREATE TABLE `Modules` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `executablePath` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `displayInMenu` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `ixLft` (`lft`),
  KEY `ixRgt` (`rgt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci