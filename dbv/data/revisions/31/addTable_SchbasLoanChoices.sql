CREATE TABLE `SchbasLoanChoices` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(8) NOT NULL,
  PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `SchbasLoanChoices` (`name`, `abbreviation`) VALUES
	('Normal', 'ln'),
	('Reduzierter Preis', 'nl'),
	('Von Zahlung befreit', 'ls'),
	('Keine Teilnahme', 'lr');