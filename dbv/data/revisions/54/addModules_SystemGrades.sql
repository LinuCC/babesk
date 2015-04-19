CALL moduleAddNewByPath(
	"Grades", 1, 0,
	"administrator/System/Grades/Grades.php",
	"System", "root/administrator/System",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"Search", 1, 0,
	"administrator/System/Grades/Search/Search.php",
	"Grades", "root/administrator/System/Grades",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);