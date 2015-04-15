-- Pvau
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Pvau',
	'root/web/Pvau',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "web/Pvau/Pvau.php"
	WHERE `ID` = @moduleId;

-- Pvp
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Pvp',
	'root/web/Pvau/Pvp',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Pvau/Pvp/Pvp.php"
	WHERE `ID` = @moduleId;