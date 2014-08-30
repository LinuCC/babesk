CALL moduleAddNewByPath(
	"RecordReceipt", 1, 1,
	"administrator/headmod_Schbas/modules/mod_SchbasAccounting/RecordReceipt.php",
	"SchbasAccounting", "root/administrator/Schbas/SchbasAccounting",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);