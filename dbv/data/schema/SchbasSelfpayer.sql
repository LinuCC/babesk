CREATE TABLE `SchbasSelfpayer` (
  `UID` int(11) NOT NULL,
  `BID` int(11) NOT NULL,
  PRIMARY KEY (`UID`,`BID`),
  KEY `ixBID` (`BID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8