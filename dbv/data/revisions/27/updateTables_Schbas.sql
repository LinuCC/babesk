-- Table Fee not used anymore
DROP TABLE SchbasFee;

CREATE TABLE `SchbasBooks` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`author` varchar(255) NOT NULL DEFAULT '',
	`publisher` varchar(255) NOT NULL DEFAULT '',
	`isbn` varchar(17) NOT NULL DEFAULT '',
	`price` float(4,2) NOT NULL DEFAULT 0.00,
	`subjectId` int(11) unsigned NOT NULL DEFAULT 0,
	`class` varchar(2) NOT NULL,
	`bundle` smallint(1) NOT NULL,
	PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `SchbasSubjects` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`abbreviation` varchar(2) NOT NULL DEFAULT '',
	`name` varchar(255) NOT NULL DEFAULT '',
	KEY `ix_abbreviation` (`abbreviation`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `SchbasAccounting` (
	`UID` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`loanChoiceId` int(11) unsigned NOT NULL DEFAULT 0,
	`payedAmount` float(4,2) NOT NULL DEFAULT 0.00,
	`amountToPay` float(4,2) NOT NULL DEFAULT 0.00,
	KEY `ixLoanChoiceId` (`loanChoiceId`),
	PRIMARY KEY(`UID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `SchbasLoanChoices` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`abbreviation` varchar(2) NOT NULL DEFAULT '',
	`name` varchar(255) NOT NULL DEFAULT '',
	KEY `ix_abbreviation` (`abbreviation`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE SchbasSelfpayer;
CREATE TABLE `SchbasSelfpayer` (
	`UID` int(11) NOT NULL,
	`BID` int(11) NOT NULL,
	KEY `ixBID` (`BID`),
	PRIMARY KEY(`UID`, `BID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `SchbasTexts` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`description` varchar(32) NOT NULL DEFAULT '',
	`title` varchar(512) NOT NULL DEFAULT '',
	`text` text NOT NULL,
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;