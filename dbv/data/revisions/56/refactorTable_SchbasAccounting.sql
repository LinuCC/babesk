ALTER TABLE `SchbasAccounting`
	CHANGE UID userId bigint(11) unsigned NOT NULL;

ALTER TABLE `SchbasAccounting` DROP PRIMARY KEY;

ALTER TABLE `SchbasAccounting`
	ADD COLUMN `id` bigint(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT;