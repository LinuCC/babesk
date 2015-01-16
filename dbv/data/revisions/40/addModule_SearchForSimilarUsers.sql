CALL moduleAddNewByPath(
	"SearchForSimilarUsers", 1, 1,
	"administrator/headmod_System/modules/mod_User/SearchForSimilarUsers.php",
	"User", "root/administrator/System/User",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);