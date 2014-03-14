CREATE TABLE `SystemUsersInGradesAndSchoolyears` (
  `userId` int(11) NOT NULL,
  `gradeId` int(11) NOT NULL,
  `schoolyearId` int(11) NOT NULL,
  PRIMARY KEY (`userId`,`gradeId`,`schoolyearId`),
  KEY `UserID` (`userId`),
  KEY `GradeID` (`gradeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8