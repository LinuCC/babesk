CREATE TABLE `SchbasLending` (
  `user_id` bigint(20) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `lend_date` date NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci