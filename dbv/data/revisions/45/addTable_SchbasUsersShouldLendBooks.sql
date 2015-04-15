CREATE TABLE `SchbasUsersShouldLendBooks` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`userId` int(11) unsigned NOT NULL,
	`bookId` int(11) unsigned NOT NULL,
	`schoolyearId` int(11) unsigned NOT NULL,
	KEY `ixSchoolyearUserBook` (`schoolyearId`, `userId`, `bookId`),
	KEY `ixSchoolyearBookUser` (`schoolyearId`, `bookId`, `userId`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;