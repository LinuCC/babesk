-- Help
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Help',
	'root/web/Help',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "web/Help/Help.php"
	WHERE `ID` = @moduleId;