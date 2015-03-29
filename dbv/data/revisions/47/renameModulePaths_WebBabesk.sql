-- Babesk
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Babesk',
	'root/web/Babesk',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "web/Babesk/Babesk.php"
	WHERE `ID` = @moduleId;

-- Order
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Order',
	'root/web/Babesk/Order',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Babesk/Order/Order.php"
	WHERE `ID` = @moduleId;

-- Menu
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Menu',
	'root/web/Babesk/Menu',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Babesk/Menu/Menu.php"
	WHERE `ID` = @moduleId;

-- Help
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Help',
	'root/web/Babesk/Help',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Babesk/Help/Help.php"
	WHERE `ID` = @moduleId;

-- ChangePassword
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ChangePassword',
	'root/web/Babesk/ChangePassword',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Babesk/ChangePassword/ChangePassword.php"
	WHERE `ID` = @moduleId;

-- Cancel
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Cancel',
	'root/web/Babesk/Cancel',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Babesk/Cancel/Cancel.php"
	WHERE `ID` = @moduleId;

-- Account
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Account',
	'root/web/Babesk/Account',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Babesk/Account/Account.php"
	WHERE `ID` = @moduleId;