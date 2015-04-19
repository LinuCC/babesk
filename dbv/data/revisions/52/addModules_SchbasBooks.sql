CALL moduleAddNewByPath(
	"Books", 1, 0,
	"administrator/Schbas/Books/Books.php",
	"Schbas", "root/administrator/Schbas",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"Search", 1, 0,
	"administrator/Schbas/Books/Search/Search.php",
	"Books", "root/administrator/Schbas/Books",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);