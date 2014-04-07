CALL moduleAddNewByPath(
	"Grade", 1, 1,
	"administrator/headmod_System/modules/mod_Grade/Grade.php",
	"System", "root/administrator/System",
	@newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);
CALL moduleAddNewByPath(
	"ShowGrades", 1, 1,
	"",
	"Grade", "root/administrator/System/Grade",
	@newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);
CALL moduleAddNewByPath(
	"AddGrade", 1, 1,
	"",
	"Grade", "root/administrator/System/Grade",
	@newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);
CALL moduleAddNewByPath(
	"DeleteGrade", 1, 1,
	"",
	"Grade", "root/administrator/System/Grade",
	@newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);
CALL moduleAddNewByPath(
	"ChangeGrade", 1, 1,
	"",
	"Grade", "root/administrator/System/Grade",
	@newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);
