CALL moduleAddNewByPath(
	"SchbasStatistics", 1, 1,
	"administrator/headmod_Statistics/modules/mod_SchbasStatistics/SchbasStatistics.php",
	"Statistics", "root/administrator/Statistics",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);