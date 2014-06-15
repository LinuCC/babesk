CALL moduleAddNewByPath(
	"UnregisterUserFromClass", 1, 1, "administrator/headmod_Kuwasys/modules/mod_Classes/UnregisterUserFromClass.php",
	"Classes", "root/administrator/Kuwasys/Classes", @newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);