CREATE TABLE IF NOT EXISTS `Fits` (
  `ID` bigint(20) NOT NULL,
  `passedTest` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `schoolyear` varchar(12) NOT NULL,
  PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;