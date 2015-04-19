-- UnregisterUserFromClass
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'UnregisterUserFromClass',
	'root/administrator/Kuwasys/Classes/UnregisterUserFromClass',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Kuwasys/Classes/UnregisterUserFromClass.php"
	WHERE `ID` = @moduleId;

-- ChangeUserStatus
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ChangeUserStatus',
	'root/administrator/Kuwasys/Classes/ChangeUserStatus',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Kuwasys/Classes/ChangeUserStatus.php"
	WHERE `ID` = @moduleId;

-- ShowBooklist
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ShowBooklist',
	'root/administrator/Schbas/Booklist/ShowBooklist',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/Booklist/ShowBooklist.php"
	WHERE `ID` = @moduleId;

-- EditBook
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'EditBook',
	'root/administrator/Schbas/Booklist/EditBook',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/Booklist/EditBook.php"
	WHERE `ID` = @moduleId;

-- DeleteBook
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'DeleteBook',
	'root/administrator/Schbas/Booklist/DeleteBook',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/Booklist/DeleteBook.php"
	WHERE `ID` = @moduleId;

-- CreateBook
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'CreateBook',
	'root/administrator/Schbas/Booklist/CreateBook',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/Booklist/CreateBook.php"
	WHERE `ID` = @moduleId;

-- Babesk
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Babesk',
	'root/publicData/Babesk',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"publicData/Babesk/Babesk.php"
	WHERE `ID` = @moduleId;

-- Menu
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Menu',
	'root/publicData/Babesk/Menu',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"publicData/Babesk/Menu/Menu.php"
	WHERE `ID` = @moduleId;

-- LoginHelp
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'LoginHelp',
	'root/publicData/GeneralPublicData/LoginHelp',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"publicData/GeneralPublicData/LoginHelp/LoginHelp.php"
	WHERE `ID` = @moduleId;

-- InputDataCheck
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'InputDataCheck',
	'root/publicData/JsDataProcessor/InputDataCheck',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"publicData/JsDataProcessor/InputDataCheck/InputDataCheck.php"
	WHERE `ID` = @moduleId;