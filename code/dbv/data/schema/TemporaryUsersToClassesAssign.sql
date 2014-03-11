CREATE TABLE `TemporaryUsersToClassesAssign` (
  `userId` int(11) unsigned NOT NULL,
  `classId` int(11) unsigned NOT NULL,
  `statusId` int(11) unsigned NOT NULL,
  `origUserId` int(11) unsigned NOT NULL,
  `origClassId` int(11) unsigned NOT NULL,
  `origStatusId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`userId`,`classId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8