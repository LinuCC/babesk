CREATE TABLE `SystemTemporaryFiles` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `until` datetime NOT NULL,
  `usage` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8