CREATE TABLE `ModuleHierarchie` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID`,`parentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8