CREATE TABLE `schbas_inventory` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `book_id` smallint(5) NOT NULL,
  `year_of_purchase` smallint(4) NOT NULL,
  `exemplar` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci