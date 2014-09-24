CALL moduleAddNewByPath(
	"VotesPerCategoryPerSchooltypeAndGrade", 1, 1,
	"administrator/headmod_Statistics/modules/mod_KuwasysStats/VotesPerCategoryPerSchooltypeAndGrade.php",
	"KuwasysStats", "root/administrator/Statistics/KuwasysStats",
	@newModuleId
);
INSERT INTO SystemGroupModuleRights (groupId, moduleId)
	VALUES (3, @newModuleId);