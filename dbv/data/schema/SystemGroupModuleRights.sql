CREATE TABLE `SystemGroupModuleRights` (
  `groupId` int(11) NOT NULL,
  `moduleId` int(11) NOT NULL,
  PRIMARY KEY (`groupId`,`moduleId`),
  KEY `ixModules` (`moduleId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8