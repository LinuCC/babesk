CALL moduleAddNewByPath(
	"Gradelevels", 1, 0,
	"administrator/System/Gradelevels/Gradelevels.php",
	"System", "root/administrator/System",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);

CALL moduleAddNewByPath(
	"Search", 1, 0,
	"administrator/System/Gradelevels/Search/Search.php",
	"Gradelevels", "root/administrator/System/Gradelevels",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);