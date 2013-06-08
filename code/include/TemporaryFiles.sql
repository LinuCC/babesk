CREATE TABLE IF NOT EXISTS `TemporaryFiles` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`location` varchar(255) NOT NULL,
	`created` DATETIME NOT NULL,
	`until` DATETIME NOT NULL,
	PRIMARY KEY (`ID`)
) AUTO_INCREMENT=1 ;