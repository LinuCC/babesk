/*Adds the new Message-Tables to the Database*/

CREATE TABLE IF NOT EXISTS `Message` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`title` varchar(255) NOT NULL,
	`text` text NOT NULL,
	`validFrom` date NOT NULL,
	`validTo` date NOT NULL,
	`originUserId` int(11) unsigned NOT NULL
		COMMENT 'The User that created the Message',
	PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `MessageReceivers` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`messageId` int(11) NOT NULL,
	`userId` int(11) unsigned NOT NULL,
	`read` tinyint(1) NOT NULL
		COMMENT 'If the Message is already read by the Recipient',
	`return` ENUM('noReturn', 'shouldReturn', 'hasReturned') NOT NULL
		COMMENT
			'If the creator wants to get the message printed out and signed',
	PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `MessageManagers` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`messageId` int(11) unsigned NOT NULL,
	`userId` int(11) unsigned NOT NULL,
	PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `MessageCarbonFootprint` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`authorId` int(11) unsigned NOT NULL,
	`savedCopies` int(6) unsigned NOT NULL,
	`returnedCopies` int(6) unsigned NOT NULL,
	PRIMARY KEY (`ID`)
) AUTO_INCREMENT=1, COMMENT='Keeps track of how much Carbon and Paper the school has saved by using Messages';

CREATE TABLE IF NOT EXISTS `MessageTemplate` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`title` varchar(255) NOT NULL,
	`text` text NOT NULL,
	PRIMARY KEY (`ID`)
) AUTO_INCREMENT=1;