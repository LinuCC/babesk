CREATE TABLE IF NOT EXISTS `LogCategories` (
	`ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`ID`),
	INDEX (`ID`),
	UNIQUE (`name`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `LogSeverities` (
	`ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	PRIMARY KEY (`ID`),
	INDEX (`ID`),
	UNIQUE (`name`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `Logs` (
	`ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`message` text NOT NULL,
	`categoryId` int(11) unsigned NOT NULL,
	`severityId` int(11) unsigned NOT NULL,
	`date` DATETIME NOT NULL,
	`additionalData` text NOT NULL,
	PRIMARY KEY (`ID`),
	INDEX (`ID`),
	INDEX (`categoryId`),
	INDEX (`severityId`)
) ENGINE=InnoDB;
