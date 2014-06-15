CREATE DEFINER=`root`@`localhost` PROCEDURE `moduleDeleteAllBetween`(
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

END