CREATE TABLE `Logs` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `categoryId` int(11) unsigned NOT NULL,
  `severityId` int(11) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `additionalData` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID` (`ID`),
  KEY `categoryId` (`categoryId`),
  KEY `severityId` (`severityId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1