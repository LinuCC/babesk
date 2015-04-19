CALL moduleAddNewByPath(
	"Add", 1, 1,
	"administrator/Schbas/BookAssignments/Add/Add.php",
	"BookAssignments", "root/administrator/Schbas/BookAssignments",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);