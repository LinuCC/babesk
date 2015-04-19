CALL moduleAddNewByPath(
	"Users", 1, 1,
	"administrator/System/Users/Users.php",
	"System", "root/administrator/System",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"Search", 1, 1,
	"administrator/System/Users/Search/Search.php",
	"Users", "root/administrator/System/Users",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);