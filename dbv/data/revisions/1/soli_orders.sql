CREATE TABLE `soli_orders` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `UID` bigint(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `IP` binary(16) NOT NULL,
  `ordertime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fetched` tinyint(1) NOT NULL DEFAULT '0',
  `mealname` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `mealprice` decimal(6,2) NOT NULL,
  `mealdate` date NOT NULL,
  `soliprice` decimal(6,2) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci