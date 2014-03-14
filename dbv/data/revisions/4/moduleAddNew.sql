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
	DECLARE EXIT HANDLER FOR SQLSTATE '42000'
		SELECT 'Could not find the Parent by the Parent-ID.';

	-- Default value, gets returned if something has gone wrong
	SET newModuleId = 0;

	SELECT rgt FROM Modules WHERE ID = parentmoduleId INTO parentRightEnd;

	IF parentRightEnd IS NOT NULL THEN
		START TRANSACTION;
		-- Inserts the new module as the first element of the parent
		UPDATE Modules SET rgt = rgt + 2 WHERE rgt >= parentRightEnd;
		UPDATE Modules SET lft = lft + 2 WHERE lft >= parentRightEnd;

		INSERT INTO Modules
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
	SELECT '';
END