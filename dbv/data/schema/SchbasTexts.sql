CREATE TABLE `SchbasTexts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(512) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8