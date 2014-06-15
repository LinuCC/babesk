CREATE TABLE `KuwasysClasses` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `description` varchar(1024) NOT NULL DEFAULT '',
  `maxRegistration` int(5) NOT NULL DEFAULT '0',
  `registrationEnabled` tinyint(1) NOT NULL DEFAULT '0',
  `unitId` int(11) unsigned NOT NULL,
  `schoolyearId` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8