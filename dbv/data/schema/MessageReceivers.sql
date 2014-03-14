CREATE TABLE `MessageReceivers` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `messageId` int(11) NOT NULL,
  `userId` int(11) unsigned NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If the Message is already read by the Recipient',
  `return` enum('noReturn','shouldReturn','hasReturned') NOT NULL COMMENT 'If the creator wants to get the message printed out and signed',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8