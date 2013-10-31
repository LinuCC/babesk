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

	DELETE FROM Modules
		WHERE lft BETWEEN leftEnd AND rightEnd;

	UPDATE Modules
		SET lft = lft - ROUND(rightEnd - leftEnd + 1)
		WHERE lft > rightEnd;

	UPDATE Modules
		SET rgt = rgt - ROUND(rightEnd - leftEnd + 1)
		WHERE rgt > rightEnd;

	COMMIT;

END
