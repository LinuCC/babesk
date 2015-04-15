RENAME TABLE `SystemUsersInGradesAndSchoolyears` TO `SystemAttendances`;

ALTER TABLE `SystemAttendances` DROP PRIMARY KEY;

ALTER TABLE `SystemAttendances`
	ADD COLUMN `id` int(11) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT;