CALL moduleAddNewByPath(
	"Show", 1, 1, "web/headmod_Kuwasys/modules/mod_ClassList/Show.php",
	"ClassList", "root/web/Kuwasys/ClassList", @newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);
CALL moduleAddNewByPath(
	"UserSelectionsApply", 1, 1,
	"web/headmod_Kuwasys/modules/mod_ClassList/UserSelectionsApply.php",
	"ClassList", "root/web/Kuwasys/ClassList",
	@newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);