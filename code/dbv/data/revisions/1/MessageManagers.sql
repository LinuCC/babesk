CREATE TABLE `MessageManagers` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `messageId` int(11) unsigned NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci