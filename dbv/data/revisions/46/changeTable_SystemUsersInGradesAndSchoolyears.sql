RENAME TABLE `SystemUsersInGradesAndSchoolyears` TO `SystemAttendants`;

ALTER TABLE `SystemAttendants` DROP PRIMARY KEY;

ALTER TABLE `SystemAttendants`
	ADD COLUMN `id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT;