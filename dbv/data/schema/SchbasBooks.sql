CREATE TABLE `SchbasBooks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `publisher` varchar(255) NOT NULL DEFAULT '',
  `isbn` varchar(17) NOT NULL DEFAULT '',
  `price` float(4,2) NOT NULL DEFAULT '0.00',
  `subjectId` int(11) unsigned NOT NULL DEFAULT '0',
  `class` varchar(2) NOT NULL,
  `bundle` smallint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8