ALTER TABLE `c1babesk`.`SystemAttendances`
	ADD UNIQUE `ixUserGradeSchoolyear` (`userId`, `gradeId`, `schoolyearId`);