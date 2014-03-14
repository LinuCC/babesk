CREATE TABLE `schbas_fee` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(5) COLLATE latin1_german2_ci NOT NULL,
  `fee_normal` float(4,2) NOT NULL,
  `fee_reduced` float(4,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci