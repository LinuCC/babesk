CREATE TABLE `UserUpdateTempConflicts` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tempUserId` int(11) unsigned NOT NULL DEFAULT '0',
  `origUserId` int(11) unsigned NOT NULL DEFAULT '0',
  `type` enum('CsvOnlyConflict','DbOnlyConflict','GradelevelConflict') NOT NULL,
  `solved` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8