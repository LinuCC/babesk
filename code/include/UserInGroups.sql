CREATE TABLE IF NOT EXISTS `UserInGroups` (
	`userId` int(11) NOT NULL,
	`groupId` int(11) NOT NULL
);

-- Some Indexing
ALTER TABLE UserInGroups
	ADD CONSTRAINT pkUserInGroups PRIMARY KEY (userId, groupId);
CREATE INDEX ixGroups ON UserInGroups (groupId)
