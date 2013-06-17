CREATE TABLE IF NOT EXISTS `Groups` (
	`ID` int(11) unsigned NOT NULL auto_increment,
	`name` varchar(255) NOT NULL,
	`lft` int(11) NOT NULL,
	`rgt` int(11) NOT NULL,
	PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1 ;

INSERT INTO Groups (ID, name, lft, rgt) VALUES (1, 'root', 1, 2)
