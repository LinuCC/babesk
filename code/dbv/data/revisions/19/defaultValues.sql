ALTER TABLE `users` MODIFY `name` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY `forename` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY `username` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY `password` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY `email` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY `telephone` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY `birthday` varchar(11) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY `last_login` varchar(11) NOT NULL DEFAULT '';
ALTER TABLE `users` MODIFY `locked` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `users` MODIFY `credit` decimal(6,2) NOT NULL DEFAULT 0.00;
ALTER TABLE `users` MODIFY `soli` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `classTeacher` MODIFY `name` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `classTeacher` MODIFY `forename` varchar(64) NOT NULL DEFAULT '';
ALTER TABLE `classTeacher` MODIFY `address` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `classTeacher` MODIFY `telephone` varchar(64) NOT NULL DEFAULT '';

ALTER TABLE `Grades` MODIFY `label` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `Grades` MODIFY `schooltypeId` int(11) NOT NULL DEFAULT 0;

-- text cant have a default value *grumblegrumble*
-- ALTER TABLE `Logs` MODIFY `additionalData` text NOT NULL DEFAULT '';

ALTER TABLE `Message` MODIFY `title` varchar(255) NOT NULL DEFAULT '';
-- text cant have a default value *grumblegrumble*
-- ALTER TABLE `Message` MODIFY `text` text NOT NULL DEFAULT '';

ALTER TABLE `MessageCarbonFootprint`
	MODIFY `savedCopies` int(6) unsigned NOT NULL DEFAULT 0;
ALTER TABLE `MessageCarbonFootprint`
	MODIFY `returnedCopies` int(6) unsigned NOT NULL DEFAULT 0;

ALTER TABLE `MessageReceivers` MODIFY `read` tinyint(1) NOT NULL DEFAULT 0
	COMMENT 'If the Message is already read by the Recipient';

ALTER TABLE `MessageTemplate` MODIFY `title` varchar(255) NOT NULL DEFAULT '';
-- text cant have a default value *grumblegrumble*
-- ALTER TABLE `MessageTemplate` MODIFY `text` text NOT NULL DEFAULT '';

ALTER TABLE `Modules` MODIFY `executablePath` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `Modules` MODIFY `enabled` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `Schooltype` MODIFY `name` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `Schooltype` MODIFY `token` varchar(3) NOT NULL DEFAULT '';

ALTER TABLE `TemporaryFiles` MODIFY `usage` varchar(64) NOT NULL DEFAULT '';

ALTER TABLE `class` MODIFY `description` varchar(1024) NOT NULL DEFAULT '';
ALTER TABLE `class` MODIFY `maxRegistration` int(5) NOT NULL DEFAULT 0;
ALTER TABLE `class` MODIFY `registrationEnabled` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `global_settings` MODIFY `value` varchar(1024) NOT NULL DEFAULT '';

ALTER TABLE `ip` MODIFY `login_tries` smallint(2) NOT NULL DEFAULT 0;

ALTER TABLE `kuwasysClassUnit`
	MODIFY `translatedName` varchar(255) NOT NULL DEFAULT '';

	-- text cant have a default value *grumblegrumble*
-- ALTER TABLE `meals` MODIFY `description` text NOT NULL DEFAULT '';
ALTER TABLE `meals` MODIFY `max_orders` int(11) NOT NULL DEFAULT 0;

-- Default Ip-address is 000.000.000.000
ALTER TABLE `orders`
	MODIFY `IP` binary(16) NOT NULL DEFAULT X'0000000000000000';

ALTER TABLE `schoolYear` MODIFY `active` tinyint(1) NOT NULL DEFAULT 0;

ALTER TABLE `soli_orders`
	MODIFY `IP` binary(16) NOT NULL DEFAULT X'0000000000000000';