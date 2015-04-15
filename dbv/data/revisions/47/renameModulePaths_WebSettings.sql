-- Settings
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Settings',
	'root/web/Settings',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "web/Settings/Settings.php"
	WHERE `ID` = @moduleId;

-- SettingsChangePassword
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SettingsChangePassword',
	'root/web/Settings/SettingsChangePassword',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Settings/SettingsChangePassword/SettingsChangePassword.php"
	WHERE `ID` = @moduleId;

-- SettingsMainMenu
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SettingsMainMenu',
	'root/web/Settings/SettingsMainMenu',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Settings/SettingsMainMenu/SettingsMainMenu.php"
	WHERE `ID` = @moduleId;

-- ChangeEmail
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ChangeEmail',
	'root/web/Settings/ChangeEmail',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Settings/ChangeEmail/ChangeEmail.php"
	WHERE `ID` = @moduleId;

-- ChangePresetPassword
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ChangePresetPassword',
	'root/web/Settings/ChangePresetPassword',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Settings/ChangePresetPassword/ChangePresetPassword.php"
	WHERE `ID` = @moduleId;