ALTER TABLE `SchbasAccounting`
	CHANGE UID userId bigint(11) unsigned NOT NULL;

ALTER TABLE `SchbasAccounting` DROP PRIMARY KEY;

ALTER TABLE `SchbasAccounting`
	ADD COLUMN `id` bigint(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT;

ALTER TABLE `SchbasAccounting`
	ADD COLUMN `schoolyearId` bigint(11) unsigned NOT NULL;

ALTER TABLE `SchbasAccounting`
	ADD UNIQUE `ixUserSchoolyear` (`schoolyearId`, `userId`);

ALTER TABLE `SchbasAccounting` ADD KEY `ixUser` (`userId`);