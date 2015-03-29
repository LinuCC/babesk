-- Statistics
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Statistics',
	'root/administrator/Statistics',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "administrator/Statistics/Statistics.php"
	WHERE `ID` = @moduleId;

-- KuwasysStats
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'KuwasysStats',
	'root/administrator/Statistics/KuwasysStats',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Statistics/KuwasysStats/KuwasysStats.php"
	WHERE `ID` = @moduleId;

-- SchbasStatistics
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SchbasStatistics',
	'root/administrator/Statistics/SchbasStatistics',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Statistics/SchbasStatistics/SchbasStatistics.php"
	WHERE `ID` = @moduleId;

-- MessageStats
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'MessageStats',
	'root/administrator/Statistics/MessageStats',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Statistics/MessageStats/MessageStats.php"
	WHERE `ID` = @moduleId;