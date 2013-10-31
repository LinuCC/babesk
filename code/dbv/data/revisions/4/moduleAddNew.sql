CREATE PROCEDURE `moduleAddNew`(
	IN modulename varchar(255),
	IN parentmoduleId int(11)
	)
BEGIN
	DECLARE parentRightEnd int(11);
	DECLARE newId int(11);

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
	BEGIN
		ROLLBACK;
	END;

	-- Custom Errorhandling, the MySQL 5.X way (before 5.5)
	DECLARE EXIT HANDLER FOR SQLSTATE '42000'
		SELECT 'Could not find the Parent by the Parent-ID.';

	START TRANSACTION;

	SELECT rgt FROM Modules WHERE ID = parentmoduleId INTO parentRightEnd;

	IF parentRightEnd IS NOT NULL THEN
		-- Inserts the new module as the first element of the parent
		UPDATE Modules SET rgt = rgt + 2 WHERE rgt >= parentRightEnd;
		UPDATE Modules SET lft = lft + 2 WHERE lft >= parentRightEnd;

		INSERT INTO Modules (name, lft, rgt) VALUES
			(modulename, parentRightEnd, parentRightEnd + 1);

		SELECT LAST_INSERT_ID() INTO newId;

	ELSE
		CALL raise_error;
	END IF;

	-- return the new ID
	SELECT newId;

	COMMIT;
END
