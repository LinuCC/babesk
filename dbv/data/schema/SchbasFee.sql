CREATE TABLE `SchbasFee` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(5) NOT NULL,
  `fee_normal` float(4,2) NOT NULL,
  `fee_reduced` float(4,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8