DROP PROCEDURE loggerAddLog;
CREATE PROCEDURE `loggerAddLog`(
	IN message text,
	IN categoryName varchar(255),
	IN severityName varchar(255),
	IN additionalData text
	)
BEGIN
	DECLARE categoryId int(11);
	DECLARE severityId int(11);

	SELECT ID FROM SystemLogCategories
		WHERE name = categoryName INTO categoryId;
	SELECT ID FROM SystemLogSeverities
		WHERE name = severityName INTO severityId;

	IF severityId IS NULL THEN
		INSERT INTO SystemLogSeverities (name) VALUES (severityName);
		SELECT LAST_INSERT_ID() INTO severityId;
	END IF;

	IF categoryId IS NULL THEN
		INSERT INTO SystemLogCategories (name) VALUES (categoryName);
		SELECT LAST_INSERT_ID() INTO categoryId;
	END IF;

	INSERT INTO SystemLogs (
			`message`, `categoryId`, `severityId`, `date`, `additionalData`)
		VALUES (
			message, categoryId, severityId, NOW(), additionalData);
END;


DROP PROCEDURE moduleAddNew;
CREATE PROCEDURE `moduleAddNew`(
	IN modulename varchar(255),
	IN isEnabled int(1),
	IN displayInMenu int(1),
	IN executablePath text,
	IN parentmoduleId int(11),
	OUT newModuleId int(11)
	)
BEGIN
	DECLARE parentRightEnd int(11);

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
		SELECT "SQL-exception while adding new module";
	END;

	-- Custom Errorhandling, the MySQL 5.X way (before 5.5)
	DECLARE EXIT HANDLER FOR SQLSTATE "42000"
		SELECT "Could not find the Parent by the Parent-ID.";

	-- Default value, gets returned if something has gone wrong
	SET newModuleId = 0;

	SELECT rgt FROM SystemModules WHERE ID = parentmoduleId INTO parentRightEnd;

	IF parentRightEnd IS NOT NULL THEN
		START TRANSACTION;
		-- Inserts the new module as the first element of the parent
		UPDATE SystemModules SET rgt = rgt + 2 WHERE rgt >= parentRightEnd;
		UPDATE SystemModules SET lft = lft + 2 WHERE lft >= parentRightEnd;

		INSERT INTO SystemModules
			(`name`, `enabled`, `displayInMenu`, `executablePath`, `lft`, `rgt`)
			VALUES (
				modulename, isEnabled, displayInMenu, executablePath,
				parentRightEnd, parentRightEnd + 1
		);
		COMMIT;

		SELECT LAST_INSERT_ID() INTO newModuleId;
	ELSE
		CALL raise_error;
	END IF;

	-- Workaround for some MySQL-Versions not working correctly with PDO-prepare
	SELECT "";
END;


DROP PROCEDURE moduleAddNewByPath;
CREATE PROCEDURE `moduleAddNewByPath` (
	IN modulename varchar(255),
	IN isEnabled int(1),
	IN displayInMenu int(1),
	IN executablePath text,
	IN directParentName varchar(255),
	IN directParentPath text,
	OUT newModId int(11)
	)
BEGIN

	DECLARE idBuffer int(11);
	DECLARE pathBuffer text;
	DECLARE resId int(11);
	DECLARE done int(1) DEFAULT 0;

	DECLARE moduleBuffer CURSOR FOR
		SELECT ID FROM SystemModules
			WHERE name = directParentName;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
	DECLARE EXIT HANDLER FOR SQLSTATE "42000"
		SELECT CONCAT('Could not find the given parentModule "',
			directParentName, '" with its path "', directParentPath, '"');

	OPEN moduleBuffer;

	-- Try to fetch the ID of the Parent by its name and Path
	REPEAT
		FETCH moduleBuffer INTO idBuffer;
		IF NOT done THEN

			-- Check if directParentPath is the same as the selected Modules
			-- Parent Path; This is because Modules are allowed to have the
			-- same name
			SELECT GROUP_CONCAT(
						parent.name ORDER BY parent.lft SEPARATOR '/'
					) INTO pathBuffer
				FROM SystemModules AS node,
					SystemModules AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
					AND node.ID = idBuffer;

			IF pathBuffer LIKE directParentPath THEN
				SELECT idBuffer INTO resId;
				SELECT 1 INTO done;
			END IF;

		END IF;
	UNTIL done END REPEAT;

	CLOSE moduleBuffer;


	-- Now add the new Module to the Parent
	IF resId IS NOT NULL THEN
		CALL moduleAddNew(modulename, isEnabled, displayInMenu, executablePath, resId, newModId);
	ELSE
		CALL raise_error;
	END IF;
END;


DROP PROCEDURE moduleDelete;
CREATE PROCEDURE `moduleDelete` (
	IN id int(11)
	)
BEGIN

	DECLARE leftEnd int(11);
	DECLARE rightEnd int(11);

	DECLARE EXIT HANDLER FOR SQLSTATE "42000"
		SELECT CONCAT("Could not find the Module by ID ", id);

	SELECT m.lft, m.rgt INTO leftEnd, rightEnd FROM SystemModules m
		WHERE m.ID = id LIMIT 1;

	IF leftEnd IS NOT NULL AND rightEnd IS NOT NULL THEN
		CALL moduleDeleteAllBetween(leftEnd, rightEnd);
	ELSE
		CALL raise_error;
	END IF;
END;



DROP PROCEDURE moduleDeleteAllBetween;
CREATE PROCEDURE `moduleDeleteAllBetween` (
	IN leftEnd int(11),
	IN rightEnd int(11)
	)
BEGIN

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
	END;

	START TRANSACTION;

	DELETE FROM SystemModules
		WHERE lft BETWEEN leftEnd AND rightEnd;

	UPDATE SystemModules
		SET lft = lft - ROUND(rightEnd - leftEnd + 1)
		WHERE lft > rightEnd;

	UPDATE SystemModules
		SET rgt = rgt - ROUND(rightEnd - leftEnd + 1)
		WHERE rgt > rightEnd;

	COMMIT;

END;


DROP PROCEDURE moduleGetByPath;
CREATE PROCEDURE `moduleGetByPath` (
	IN modulename varchar(255),
	IN modulepath text,
	OUT moduleId int(11)
)
BEGIN
	DECLARE idBuffer int(11);
	DECLARE pathBuffer text;
	DECLARE done int(1) DEFAULT 0;

	DECLARE moduleBuffer CURSOR FOR
		SELECT ID FROM SystemModules
			WHERE name = modulename;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
	DECLARE EXIT HANDLER FOR SQLSTATE "42000"
		SELECT CONCAT("Could not find the given module ",
			modulename, " with its path ", modulepath);

	OPEN moduleBuffer;

	-- Try to fetch the ID of the Parent by its name and Path
	REPEAT
		FETCH moduleBuffer INTO idBuffer;
		IF NOT done THEN

			-- Check if modulepath is the same as the selected Modules
			-- Path; This is because Modules are allowed to have the
			-- same name
			SELECT GROUP_CONCAT(parent.name SEPARATOR "/") INTO pathBuffer
				FROM SystemModules AS node,
					SystemModules AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
					AND node.ID = idBuffer
				ORDER BY node.lft;

			IF pathBuffer LIKE modulepath THEN
				SELECT idBuffer INTO moduleid;
				SELECT 1 INTO done;
			END IF;

		END IF;
	UNTIL done END REPEAT;

	CLOSE moduleBuffer;

END;