-- Schbas
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Schbas',
	'root/web/Schbas',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "web/Schbas/Schbas.php"
	WHERE `ID` = @moduleId;

-- LoanSystem
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'LoanSystem',
	'root/web/Schbas/LoanSystem',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"web/Schbas/LoanSystem/LoanSystem.php"
	WHERE `ID` = @moduleId;