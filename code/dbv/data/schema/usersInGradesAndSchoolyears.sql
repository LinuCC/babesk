CREATE TABLE `usersInGradesAndSchoolyears` (
  `userId` int(11) NOT NULL,
  `gradeId` int(11) NOT NULL,
  `schoolyearId` int(11) NOT NULL,
  PRIMARY KEY (`userId`,`gradeId`,`schoolyearId`),
  KEY `UserID` (`userId`),
  KEY `GradeID` (`gradeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german2_ci