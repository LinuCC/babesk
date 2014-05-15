CREATE TABLE `SchbasAccounting` (
  `UID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `loanChoiceId` int(11) unsigned NOT NULL DEFAULT '0',
  `payedAmount` float(4,2) NOT NULL DEFAULT '0.00',
  `amountToPay` float(4,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`UID`),
  KEY `ixLoanChoiceId` (`loanChoiceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8