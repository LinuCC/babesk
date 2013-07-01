CREATE TABLE IF NOT EXISTS `GroupModuleRights` (
	`groupId` int(11) NOT NULL,
	`moduleId` int(11) NOT NULL,
	`accessAllowed` boolean NOT NULL
);

-- Some Indexing
ALTER TABLE GroupModuleRights
	ADD CONSTRAINT pkGroupModuleRights PRIMARY KEY (groupId, moduleId);
CREATE INDEX ixModules ON GroupModuleRights (moduleId)
