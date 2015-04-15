-- System
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'System',
	'root/administrator/System',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "administrator/System/System.php"
	WHERE `ID` = @moduleId;

-- ModuleSettings
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/ModuleSettings/ModuleSettings.php"
	-- Somehow he wont change the value if I try to fetch per moduleGetByPath
	WHERE `name` = "ModuleSettings";

-- GroupSettings
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/GroupSettings/GroupSettings.php"
	-- Somehow he wont change the value if I try to fetch per moduleGetByPath
	WHERE `name` = "GroupSettings";

-- Schooltype
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Schooltype',
	'root/administrator/System/Schooltype',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Schooltype/Schooltype.php"
	WHERE `ID` = @moduleId;

-- CardChange
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'CardChange',
	'root/administrator/System/CardChange',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/CardChange/CardChange.php"
	WHERE `ID` = @moduleId;

-- WebHomepageSettings
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'WebHomepageSettings',
	'root/administrator/System/WebHomepageSettings',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/WebHomepageSettings/WebHomepageSettings.php"
	WHERE `ID` = @moduleId;

-- PresetPassword
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'PresetPassword',
	'root/administrator/System/PresetPassword',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/PresetPassword/PresetPassword.php"
	WHERE `ID` = @moduleId;

-- EmailConfiguration
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'EmailConfiguration',
	'root/administrator/System/EmailConfiguration',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/EmailConfiguration/EmailConfiguration.php"
	WHERE `ID` = @moduleId;

-- CardInfo
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'CardInfo',
	'root/administrator/System/CardInfo',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/CardInfo/CardInfo.php"
	WHERE `ID` = @moduleId;

-- Logs
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Logs',
	'root/administrator/System/Logs',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Logs/Logs.php"
	WHERE `ID` = @moduleId;

-- Groups
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Groups',
	'root/administrator/System/Groups',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Groups/Groups.php"
	WHERE `ID` = @moduleId;

-- Help
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Help',
	'root/administrator/System/Help',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Help/Help.php"
	WHERE `ID` = @moduleId;

-- SpecialCourse
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SpecialCourse',
	'root/administrator/System/SpecialCourse',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/SpecialCourse/SpecialCourse.php"
	WHERE `ID` = @moduleId;

-- ForeignLanguage
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ForeignLanguage',
	'root/administrator/System/ForeignLanguage',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/ForeignLanguage/ForeignLanguage.php"
	WHERE `ID` = @moduleId;

-- Religion
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Religion',
	'root/administrator/System/Religion',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Religion/Religion.php"
	WHERE `ID` = @moduleId;

-- User
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'User',
	'root/administrator/System/User',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/User.php"
	WHERE `ID` = @moduleId;

-- Schoolyear
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Schoolyear',
	'root/administrator/System/Schoolyear',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Schoolyear/Schoolyear.php"
	WHERE `ID` = @moduleId;

-- PersonalBookmarks
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'PersonalBookmarks',
	'root/administrator/System/PersonalBookmarks',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/PersonalBookmarks/PersonalBookmarks.php"
	WHERE `ID` = @moduleId;

-- Grade
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Grade',
	'root/administrator/System/Grade',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Grade/Grade.php"
	WHERE `ID` = @moduleId;



-- ShowLogs
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ShowLogs',
	'root/administrator/System/Logs/ShowLogs',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Logs/ShowLogs.php"
	WHERE `ID` = @moduleId;

-- DeleteLogs
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'DeleteLogs',
	'root/administrator/System/Logs/DeleteLogs',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/Logs/DeleteLogs.php"
	WHERE `ID` = @moduleId;

-- Multiselection
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Multiselection',
	'root/administrator/System/User/DisplayAll/Multiselection',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/DisplayAll/Multiselection/Multiselection.php"
	WHERE `ID` = @moduleId;

-- ActionsGet
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ActionsGet',
	'root/administrator/System/User/DisplayAll/Multiselection/ActionsGet',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/DisplayAll/Multiselection/ActionsGet.php"
	WHERE `ID` = @moduleId;

-- ActionExecute
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ActionExecute',
	'root/administrator/System/User/DisplayAll/Multiselection/ActionExecute',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/DisplayAll/Multiselection/ActionExecute.php"
	WHERE `ID` = @moduleId;

-- Change
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Change',
	'root/administrator/System/User/Change',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/Change.php"
	WHERE `ID` = @moduleId;

-- DisplayChange
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'DisplayChange',
	'root/administrator/System/User/DisplayChange',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/DisplayChange.php"
	WHERE `ID` = @moduleId;

-- ResetAllUserPasswords
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ResetAllUserPasswords',
	'root/administrator/System/User/ResetAllUserPasswords',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/ResetAllUserPasswords/ResetAllUserPasswords.php"
	WHERE `ID` = @moduleId;

-- UserUpdateWithSchoolyearChange
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'UserUpdateWithSchoolyearChange',
	'root/administrator/System/User/UserUpdateWithSchoolyearChange',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/UserUpdateWithSchoolyearChange/UserUpdateWithSchoolyearChange.php"
	WHERE `ID` = @moduleId;

-- NewSession
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'NewSession',
	'root/administrator/System/User/UserUpdateWithSchoolyearChange/NewSession',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/UserUpdateWithSchoolyearChange/NewSession.php"
	WHERE `ID` = @moduleId;

-- CsvImport
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'CsvImport',
	'root/administrator/System/User/UserUpdateWithSchoolyearChange/CsvImport',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/UserUpdateWithSchoolyearChange/CsvImport.php"
	WHERE `ID` = @moduleId;

-- SessionMenu
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SessionMenu',
	'root/administrator/System/User/UserUpdateWithSchoolyearChange/SessionMenu',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/UserUpdateWithSchoolyearChange/SessionMenu.php"
	WHERE `ID` = @moduleId;

-- ConflictsResolve
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ConflictsResolve',
	'root/administrator/System/User/UserUpdateWithSchoolyearChange/SessionMenu/ConflictsResolve',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/UserUpdateWithSchoolyearChange/ConflictsResolve.php"
	WHERE `ID` = @moduleId;

-- ChangesList
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ChangesList',
	'root/administrator/System/User/UserUpdateWithSchoolyearChange/SessionMenu/ChangesList',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/UserUpdateWithSchoolyearChange/ChangesList.php"
	WHERE `ID` = @moduleId;

-- ChangeExecute
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ChangeExecute',
	'root/administrator/System/User/UserUpdateWithSchoolyearChange/ChangeExecute',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/UserUpdateWithSchoolyearChange/ChangeExecute.php"
	WHERE `ID` = @moduleId;

-- UserCsvImport
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'UserCsvImport',
	'root/administrator/System/User/UserCsvImport',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/System/User/UserCsvImport.php"
	WHERE `ID` = @moduleId;