CALL moduleAddNewByPath(
	"ShowLogs", 1, 1,
	"administrator/headmod_System/modules/mod_Logs/ShowLogs.php",
	"Logs", "root/administrator/System/Logs",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"DeleteLogs", 1, 1,
	"administrator/headmod_System/modules/mod_Logs/DeleteLogs.php",
	"Logs", "root/administrator/System/Logs",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);