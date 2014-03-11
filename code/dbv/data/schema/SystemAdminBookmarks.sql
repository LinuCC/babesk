CREATE TABLE `SystemAdminBookmarks` (
  `uid` bigint(11) NOT NULL,
  `bmid` enum('0','1','2','3','4') NOT NULL,
  `mid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`bmid`,`mid`),
  KEY `ixUserId` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8