CREATE TABLE `MessageCarbonFootprint` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `authorId` int(11) unsigned NOT NULL,
  `savedCopies` int(6) unsigned NOT NULL,
  `returnedCopies` int(6) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci COMMENT='Keeps track of how much Carbon and Paper the school has saved by using Messages'