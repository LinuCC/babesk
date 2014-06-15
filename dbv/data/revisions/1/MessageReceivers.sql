CREATE TABLE `MessageReceivers` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `messageId` int(11) NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `read` tinyint(1) NOT NULL COMMENT 'If the Message is already read by the Recipient',
  `return` enum('noReturn','shouldReturn','hasReturned') COLLATE latin1_german2_ci NOT NULL COMMENT 'If the creator wants to get the message printed out and signed',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci