CREATE TABLE `MessageCarbonFootprint` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `authorId` int(11) unsigned NOT NULL,
  `savedCopies` int(6) unsigned NOT NULL DEFAULT '0',
  `returnedCopies` int(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Keeps track of how much Carbon and Paper the school has saved by using Messages'