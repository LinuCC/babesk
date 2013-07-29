
SET @activeSchoolyear := (SELECT ID FROM schoolYear WHERE active = "1");

ALTER TABLE jointUsersInGrade
ADD schoolyearId int(11) NOT NULL;

UPDATE jointUsersInGrade
	SET schoolyearId = @activeSchoolyear
	WHERE schoolyearId = '';

DROP TABLE jointUsersInSchoolYear;
DROP TABLE jointGradeInSchoolYear;

RENAME TABLE jointUsersInGrade TO usersInGradesAndSchoolyears;

INSERT INTO grade (label, gradeValue, schooltypeId)
	VALUES ('Keine Klasse', 0, 0);

ALTER TABLE usersInGradesAndSchoolyears
	CHANGE UserID userId int(11) NOT NULL,
	CHANGE GradeID gradeId int(11) NOT NULL,
	MODIFY ID INT NOT NULL, -- remove autoincrement for Primary Key change
	DROP PRIMARY KEY,
	ADD PRIMARY KEY(userId, gradeId, schoolyearId),
	DROP COLUMN ID;
