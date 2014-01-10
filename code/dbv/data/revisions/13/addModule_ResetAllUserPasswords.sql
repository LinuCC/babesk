CALL moduleAddNewByPath("ResetAllUserPasswords", 1, 1, "administrator/headmod_System/modules/mod_User/ResetAllUserPasswords/ResetAllUserPasswords.php", "User", "root/administrator/System/User", @newModuleId);

INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);