CALL moduleAddNewByPath(
	"GlobalSettings", 1, 0,
	"administrator/System/GlobalSettings/GlobalSettings.php",
	"System", "root/administrator/System",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"Change", 1, 0,
	"administrator/System/GlobalSettings/Change/Change.php",
	"GlobalSettings", "root/administrator/System/GlobalSettings",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);