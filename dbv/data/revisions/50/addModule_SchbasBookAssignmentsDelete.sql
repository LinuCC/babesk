CALL moduleAddNewByPath(
	"Delete", 1, 0,
	"administrator/Schbas/BookAssignments/Delete/Delete.php",
	"BookAssignments", "root/administrator/Schbas/BookAssignments",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);