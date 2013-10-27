CREATE TABLE `schbas_spezific` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `book_id` int(10) NOT NULL,
  `class` varchar(5) COLLATE latin1_german2_ci NOT NULL,
  `notify_only` tinyint(1) NOT NULL,
  `info` longtext COLLATE latin1_german2_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci