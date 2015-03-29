-- Fits
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Fits',
	'root/administrator/Fits',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "administrator/Fits/Fits.php"
	WHERE `ID` = @moduleId;

-- FitsSettings
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'FitsSettings',
	'root/administrator/Fits/FitsSettings',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Fits/FitsSettings/FitsSettings.php"
	WHERE `ID` = @moduleId;

-- FitsCheck
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'FitsCheck',
	'root/administrator/Fits/FitsCheck',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Fits/FitsCheck/FitsCheck.php"
	WHERE `ID` = @moduleId;