CALL moduleAddNewByPath(
	"GradeOverview", 1, 1,
	"administrator/headmod_Kuwasys/modules/mod_Classes/GradeOverview/GradeOverview.php",
	"Classes", "root/administrator/Kuwasys/Classes",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);