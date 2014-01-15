CALL moduleAddNewByPath("UserUpdateWithSchoolyearChange", 1, 1, "administrator/headmod_System/modules/mod_User/UserUpdateWithSchoolyearChange/UserUpdateWithSchoolyearChange.php", "User", "root/administrator/System/User", @newModuleId);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);

CALL moduleAddNewByPath("NewSession", 1, 1, "administrator/headmod_System/modules/mod_User/UserUpdateWithSchoolyearChange/NewSession.php", "UserUpdateWithSchoolyearChange", "root/administrator/System/User/UserUpdateWithSchoolyearChange", @newModuleId);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);

CALL moduleAddNewByPath("CsvImport", 1, 1, "administrator/headmod_System/modules/mod_User/UserUpdateWithSchoolyearChange/CsvImport.php", "UserUpdateWithSchoolyearChange", "root/administrator/System/User/UserUpdateWithSchoolyearChange", @newModuleId);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);

CALL moduleAddNewByPath("SessionMenu", 1, 1, "administrator/headmod_System/modules/mod_User/UserUpdateWithSchoolyearChange/SessionMenu.php", "UserUpdateWithSchoolyearChange", "root/administrator/System/User/UserUpdateWithSchoolyearChange", @newModuleId);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);

CALL moduleAddNewByPath("ChangeExecute", 1, 1, "administrator/headmod_System/modules/mod_User/UserUpdateWithSchoolyearChange/ChangeExecute.php", "UserUpdateWithSchoolyearChange", "root/administrator/System/User/UserUpdateWithSchoolyearChange", @newModuleId);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);

CALL moduleAddNewByPath("ConflictsResolve", 1, 1, "administrator/headmod_System/modules/mod_User/UserUpdateWithSchoolyearChange/ConflictsResolve.php", "SessionMenu", "root/administrator/System/User/UserUpdateWithSchoolyearChange/SessionMenu", @newModuleId);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);

CALL moduleAddNewByPath("ChangesList", 1, 1, "administrator/headmod_System/modules/mod_User/UserUpdateWithSchoolyearChange/ChangesList.php", "SessionMenu", "root/administrator/System/User/UserUpdateWithSchoolyearChange/SessionMenu", @newModuleId);
INSERT INTO GroupModuleRights (groupId, moduleId) VALUES
	(3, @newModuleId);