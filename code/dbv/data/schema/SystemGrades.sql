CREATE TABLE `SystemGrades` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL DEFAULT '',
  `gradelevel` int(3) DEFAULT NULL,
  `schooltypeId` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8