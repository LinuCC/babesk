CREATE TABLE IF NOT EXISTS `Schooltype` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1 ;

ALTER TABLE `grade` ADD `schooltypeId` int(11) NOT NULL;