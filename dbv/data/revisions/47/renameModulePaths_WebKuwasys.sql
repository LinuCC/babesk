-- Kuwasys
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Kuwasys',
	'root/web/Kuwasys',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "web/Kuwasys/Kuwasys.php"
	WHERE `ID` = @moduleId;

-- ClassList
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ClassList',
	'root/web/Kuwasys/ClassList',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Kuwasys/ClassList/ClassList.php"
	WHERE `ID` = @moduleId;

-- Show
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Show',
	'root/web/Kuwasys/ClassList/Show',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Kuwasys/ClassList/Show.php"
	WHERE `ID` = @moduleId;

-- UserSelectionsApply
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'UserSelectionsApply',
	'root/web/Kuwasys/ClassList/UserSelectionsApply',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Kuwasys/ClassList/UserSelectionsApply.php"
	WHERE `ID` = @moduleId;

-- MainMenu
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'MainMenu',
	'root/web/Kuwasys/MainMenu',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Kuwasys/MainMenu/MainMenu.php"
	WHERE `ID` = @moduleId;

-- ClassDetails
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ClassDetails',
	'root/web/Kuwasys/ClassDetails',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Kuwasys/ClassDetails/ClassDetails.php"
	WHERE `ID` = @moduleId;