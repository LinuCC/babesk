CREATE DEFINER=`root`@`localhost` PROCEDURE `moduleDelete`(
	IN id int(11)
	)
BEGIN

	DECLARE leftEnd int(11);
	DECLARE rightEnd int(11);

	DECLARE EXIT HANDLER FOR SQLSTATE '42000'
		SELECT CONCAT('Could not find the Module by ID ', id);

	SELECT m.lft, m.rgt INTO leftEnd, rightEnd FROM Modules m
		WHERE m.ID = id LIMIT 1;

	IF leftEnd IS NOT NULL AND rightEnd IS NOT NULL THEN
		CALL moduleDeleteAllBetween(leftEnd, rightEnd);
	ELSE
		CALL raise_error;
	END IF;


END