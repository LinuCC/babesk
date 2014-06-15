CREATE TABLE `BabeskOrders` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `MID` int(11) unsigned NOT NULL,
  `UID` bigint(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `IP` binary(16) NOT NULL,
  `ordertime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fetched` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci