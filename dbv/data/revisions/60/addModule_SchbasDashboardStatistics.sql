CALL moduleAddNewByPath(
	"Statistics", 1, 0,
	"administrator/Schbas/Dashboard/Statistics/Statistics.php",
	"Dashboard", "root/administrator/Schbas/Dashboard",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);