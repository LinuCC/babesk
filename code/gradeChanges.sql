
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
