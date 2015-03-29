-- Fits
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Fits',
	'root/web/Fits',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "web/Fits/Fits.php"
	WHERE `ID` = @moduleId;

-- Zeugnis
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Zeugnis',
	'root/web/Fits/Zeugnis',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Fits/Zeugnis/Zeugnis.php"
	WHERE `ID` = @moduleId;

-- Quiz
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Quiz',
	'root/web/Fits/Quiz',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Fits/Quiz/Quiz.php"
	WHERE `ID` = @moduleId;

-- Fmenu
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Fmenu',
	'root/web/Fits/Fmenu',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Fits/Fmenu/Fmenu.php"
	WHERE `ID` = @moduleId;