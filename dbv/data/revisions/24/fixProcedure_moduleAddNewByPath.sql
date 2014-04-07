-- Revisions before were rewritten (sorry, but had to be done...) to
-- accommodate the fix, but fix already installed systems, too

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