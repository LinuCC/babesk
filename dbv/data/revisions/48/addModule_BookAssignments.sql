CALL moduleAddNewByPath(
	"BookAssignments", 1, 1,
	"administrator/Schbas/BookAssignments/BookAssignments.php",
	"Schbas", "root/administrator/Schbas",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"Generate", 1, 1,
	"administrator/Schbas/BookAssignments/Generate/Generate.php",
	"BookAssignments", "root/administrator/Schbas/BookAssignments",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"View", 1, 1,
	"administrator/Schbas/BookAssignments/View/View.php",
	"BookAssignments", "root/administrator/Schbas/BookAssignments",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);