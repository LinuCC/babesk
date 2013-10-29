CREATE TABLE `Message` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `text` text COLLATE latin1_german2_ci NOT NULL,
  `validFrom` date NOT NULL,
  `validTo` date NOT NULL,
  `originUserId` int(11) unsigned NOT NULL COMMENT 'The User that created the Message',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci