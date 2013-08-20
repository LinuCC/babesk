CREATE TABLE IF NOT EXISTS usercreditsRecharges (
`ID` int(11) unsigned NOT NULL auto_increment,
`userId` int(11) unsigned NOT NULL,
`rechargingUserId` int(11) unsigned NOT NULL,
`rechargeAmount` decimal(6,2) NOT NULL,
`datetime` DATETIME NOT NULL,
PRIMARY KEY (`ID`)
);
