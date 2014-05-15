CREATE TABLE `SchbasLoanChoices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `abbreviation` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ix_abbreviation` (`abbreviation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8