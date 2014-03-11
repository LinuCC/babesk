CREATE TABLE `KuwasysClassteachersInClasses` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ClassTeacherID` int(11) unsigned NOT NULL,
  `ClassID` int(11) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8