CREATE TABLE IF NOT EXISTS `adminBookmarks` (
  `uid` bigint(11) NOT NULL,
  `bmid` enum('0','1','2','3','4') NOT NULL,
  `mid` int(11) NOT NULL,
  KEY `ixUserId` (`uid`),
  KEY `ixBmidMid` (`bmid`, `mid`),
  PRIMARY KEY(`uid`, `bmid`, `mid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;