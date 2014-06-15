UPDATE `SystemModules` SET `executablePath` = 'administrator/headmod_System/modules/mod_User/DisplayChange.php' WHERE name = 'DisplayChange';

CALL `moduleGetByPath` (
	'Change',
	'root/administrator/System/User/Change',
	@changeId
);

UPDATE `SystemModules` SET `executablePath` = 'administrator/headmod_System/modules/mod_User/Change.php' WHERE ID = @changeId;
