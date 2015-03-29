-- Messages
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Messages',
	'root/web/Messages',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "web/Messages/Messages.php"
	WHERE `ID` = @moduleId;

-- MessageMainMenu
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'MessageMainMenu',
	'root/web/Messages/MessageMainMenu',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Messages/MessageMainMenu/MessageMainMenu.php"
	WHERE `ID` = @moduleId;

-- MessageAdmin
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'MessageAdmin',
	'root/web/Messages/MessageAdmin',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Messages/MessageAdmin/MessageAdmin.php"
	WHERE `ID` = @moduleId;