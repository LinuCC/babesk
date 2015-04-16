CALL moduleAddNewByPath(
	"Account", 1, 1,
	"web/Settings/Account/Account.php",
	"Settings", "root/web/Settings",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);