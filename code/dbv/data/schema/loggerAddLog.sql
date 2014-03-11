CREATE DEFINER=`root`@`localhost` PROCEDURE `loggerAddLog`(
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
END