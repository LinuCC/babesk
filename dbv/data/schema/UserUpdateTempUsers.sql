CREATE TABLE `UserUpdateTempUsers` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `origUserId` int(11) unsigned NOT NULL DEFAULT '0',
  `forename` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `newUsername` varchar(64) DEFAULT NULL,
  `newTelephone` varchar(64) DEFAULT NULL,
  `newEmail` varchar(64) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gradelevel` int(3) NOT NULL DEFAULT '0',
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8