-- Messages
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Messages',
	'root/administrator/Messages',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "administrator/Messages/Messages.php"
	WHERE `ID` = @moduleId;

-- MessageAdmin
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'MessageAdmin',
	'root/administrator/Messages/MessageAdmin',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Messages/MessageAdmin/MessageAdmin.php"
	WHERE `ID` = @moduleId;

-- MessageTemplate
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'MessageTemplate',
	'root/administrator/Messages/MessageTemplate',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Messages/MessageTemplate/MessageTemplate.php"
	WHERE `ID` = @moduleId;

-- MessageAuthor
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'MessageAuthor',
	'root/administrator/Messages/MessageAuthor',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Messages/MessageAuthor/MessageAuthor.php"
	WHERE `ID` = @moduleId;