
-- Schbas
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Schbas',
	'root/administrator/Schbas',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "administrator/Schbas/Schbas.php"
	WHERE `ID` = @moduleId;

-- SchbasMessages
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SchbasMessages',
	'root/administrator/Schbas/SchbasMessages',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/SchbasMessages/SchbasMessages.php"
	WHERE `ID` = @moduleId;

-- SchbasAccounting
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SchbasAccounting',
	'root/administrator/Schbas/SchbasAccounting',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/SchbasAccounting/SchbasAccounting.php"
	WHERE `ID` = @moduleId;

-- SchbasSettings
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SchbasSettings',
	'root/administrator/Schbas/SchbasSettings',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/SchbasSettings/SchbasSettings.php"
	WHERE `ID` = @moduleId;

-- BookInfo
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'BookInfo',
	'root/administrator/Schbas/BookInfo',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/BookInfo/BookInfo.php"
	WHERE `ID` = @moduleId;

-- Retour
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Retour',
	'root/administrator/Schbas/Retour',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/Retour/Retour.php"
	WHERE `ID` = @moduleId;

-- Loan
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Loan',
	'root/administrator/Schbas/Loan',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/Loan/Loan.php"
	WHERE `ID` = @moduleId;

-- Inventory
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Inventory',
	'root/administrator/Schbas/Inventory',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/Inventory/Inventory.php"
	WHERE `ID` = @moduleId;

-- Booklist
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Booklist',
	'root/administrator/Schbas/Booklist',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/Booklist/Booklist.php"
	WHERE `ID` = @moduleId;

-- RecordReceipt
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'RecordReceipt',
	'root/administrator/Schbas/SchbasAccounting/RecordReceipt',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Schbas/SchbasAccounting/RecordReceipt.php"
	WHERE `ID` = @moduleId;