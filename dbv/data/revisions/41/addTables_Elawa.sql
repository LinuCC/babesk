CREATE TABLE `SystemRooms` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(32) NOT NULL,
	KEY `ixName` (`name`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ElawaCategories` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	KEY `ixName` (`name`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ElawaMeetings` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`visitorId` int(11) unsigned NOT NULL,
	`hostId` int(11) unsigned NOT NULL,
	`categoryId` int(11) unsigned NOT NULL,
	`roomId` int(11) unsigned NOT NULL DEFAULT 0,
	`time` TIME NOT NULL DEFAULT '00:00:00',
	`length` TIME NOT NULL DEFAULT '00:00:00',
	`isDisabled` bit(1) NOT NULL DEFAULT 0,
	KEY `ixHostId` (`hostId`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ElawaDefaultMeetingRooms` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`hostId` int(11) unsigned NOT NULL,
	`categoryId` int(11) unsigned NOT NULL,
	`roomId` int(11) unsigned NOT NULL,
	KEY `ixHostId` (`hostId`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ElawaDefaultMeetingTimes` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`categoryId` int(11) unsigned NOT NULL,
	`time` TIME NOT NULL DEFAULT '00:00:00',
	`length` TIME NOT NULL DEFAULT '00:00:00',
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;