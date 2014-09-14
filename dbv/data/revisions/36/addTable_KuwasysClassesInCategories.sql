CREATE TABLE `KuwasysClassesInCategories` (
	`classId` int(11) NOT NULL,
	`categoryId` int(11) NOT NULL,
	KEY `ixClasses` (`classId`),
	KEY `ixCategories` (`categoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `KuwasysClassesInCategories` (classId, categoryId)
	SELECT ID, unitId FROM `KuwasysClasses`;