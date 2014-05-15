CREATE TABLE `BabeskMeals` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_german2_ci NOT NULL,
  `description` text COLLATE latin1_german2_ci NOT NULL,
  `price_class` smallint(6) NOT NULL,
  `date` date NOT NULL,
  `max_orders` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci