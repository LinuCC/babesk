CALL moduleAddNewByPath("DeletedUserShowPdf", 1, 1, "", "User", "root/administrator/System/User", @newModuleId);

INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);