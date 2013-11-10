CREATE PROCEDURE `moduleGetIdByPath` (
	IN modulename varchar(255) CHARSET utf8,
	IN modulePath text CHARSET utf8,
	OUT moduleId int(11)
	)
BEGIN

	DECLARE done int(1) DEFAULT 0;
	DECLARE idBuffer int(11);
	DECLARE pathBuffer text;

	DECLARE moduleBuffer CURSOR FOR
		SELECT ID FROM Modules
			WHERE name = modulename;


	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
	DECLARE EXIT HANDLER FOR SQLSTATE '42000'
		SELECT CONCAT('Could not find the module by name "',
			modulename, '" with its path "', modulePath);

	SET moduleId = 0;

	OPEN moduleBuffer;

	REPEAT
		FETCH moduleBuffer INTO idBuffer;
		IF NOT done THEN

			SELECT GROUP_CONCAT(parent.name SEPARATOR '/') INTO pathBuffer
				FROM Modules AS node,
					Modules AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
					AND node.ID = idBuffer
				ORDER BY node.lft;

			IF pathBuffer LIKE modulePath THEN
				SELECT idBuffer INTO moduleId;
				SELECT 1 INTO done;
			END IF;

		END IF;
	UNTIL done END REPEAT;

	CLOSE moduleBuffer;

END