CALL moduleAddNewByPath(
	"Add", 1, 0,
	"administrator/Schbas/Inventory/Add/Add.php",
	"Inventory", "root/administrator/Schbas/Inventory",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"Delete", 1, 0,
	"administrator/Schbas/Inventory/Delete/Delete.php",
	"Inventory", "root/administrator/Schbas/Inventory",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);