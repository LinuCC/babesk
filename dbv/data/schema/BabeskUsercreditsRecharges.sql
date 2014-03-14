CREATE TABLE `BabeskUsercreditsRecharges` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `rechargingUserId` int(11) unsigned NOT NULL,
  `rechargeAmount` decimal(6,2) NOT NULL,
  `datetime` datetime NOT NULL,
  `soli` tinyint(1) NOT NULL COMMENT 'If the User had a valid Solicoupon at the time he recharged',
  `isSoli` tinyint(1) NOT NULL COMMENT 'If the User had a valid Solicoupon at the time he recharged',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8