/*Adds the new Message-Tables to the Database*/

CREATE TABLE IF NOT EXISTS `Message` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`title` varchar(255) NOT NULL,
	`text` text NOT NULL,
	`validFrom` date NOT NULL,
	`validTo` date NOT NULL,
	`originUserId` int(11) NOT NULL
		COMMENT 'The User that created the Message',
	PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `MessageReceivers` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`messageId` int(11) NOT NULL,
	`userId` int(11) NOT NULL,
	`read` tinyint(1) NOT NULL
		COMMENT 'Check if the Message is already read by the Recipient',
	PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `MessageManagers` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`messageId` int(11) NOT NULL,
	`userId` int(11) NOT NULL,
	PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1;