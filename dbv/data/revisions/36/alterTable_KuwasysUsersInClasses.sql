RENAME TABLE KuwasysUsersInClasses TO KuwasysUsersInClassesAndCategories;
ALTER TABLE KuwasysUsersInClassesAndCategories
	ADD categoryId int(11) NOT NULL;

UPDATE `KuwasysUsersInClassesAndCategories` uicc, `KuwasysClasses` c
	SET uicc.categoryId = c.unitId
	WHERE uicc.ClassID = c.ID;