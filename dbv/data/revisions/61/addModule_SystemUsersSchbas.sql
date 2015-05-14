CALL moduleAddNewByPath(
	"Schbas", 1, 0,
	"administrator/System/Users/Schbas/Schbas.php",
	"Users", "root/administrator/System/Users",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"Search", 1, 0,
	"administrator/System/Users/Schbas/Search/Search.php",
	"Schbas", "root/administrator/System/Users/Schbas",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);