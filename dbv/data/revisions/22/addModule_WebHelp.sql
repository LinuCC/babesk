CALL moduleAddNewByPath(
	"Help", 1, 1, "web/headmod_Help/Help.php",
	"web", "root/web", @newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);