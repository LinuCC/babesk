CALL moduleAddNewByPath(
	"ChangeUserStatus", 1, 1, "administrator/headmod_Kuwasys/modules/mod_Classes/ChangeUserStatus.php",
	"Classes", "root/administrator/Kuwasys/Classes", @newModuleId
);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES (3, @newModuleId);