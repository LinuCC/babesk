CREATE TABLE `MessageMessages` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `text` mediumtext NOT NULL,
  `validFrom` date NOT NULL,
  `validTo` date NOT NULL,
  `originUserId` int(11) unsigned NOT NULL COMMENT 'The User that created the Message',
  `GID` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8