CREATE TABLE `SystemUsersInGroups` (
  `userId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  PRIMARY KEY (`userId`,`groupId`),
  KEY `ixGroups` (`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8