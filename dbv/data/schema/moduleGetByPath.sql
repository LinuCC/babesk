CREATE DEFINER=`root`@`localhost` PROCEDURE `moduleGetByPath`(
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

END