DELIMITER //



-- Calculates the levenshtein-Distance between two strings
DROP FUNCTION IF EXISTS levenshtein //
CREATE FUNCTION `levenshtein`( s1 text, s2 text) RETURNS int(11) DETERMINISTIC
BEGIN
	DECLARE s1_len, s2_len, i, j, c, c_temp, cost INT;
	DECLARE s1_char CHAR;
	DECLARE cv0, cv1 text;
	SET s1_len = CHAR_LENGTH(s1), s2_len = CHAR_LENGTH(s2), cv1 = 0x00, j = 1, i = 1, c = 0;
	IF s1 = s2 THEN
		RETURN 0;
	ELSEIF s1_len = 0 THEN
		RETURN s2_len;
	ELSEIF s2_len = 0 THEN
		RETURN s1_len;
	ELSE
		WHILE j <= s2_len DO
			SET cv1 = CONCAT(cv1, UNHEX(HEX(j))), j = j + 1;
		END WHILE;
		WHILE i <= s1_len DO
			SET s1_char = SUBSTRING(s1, i, 1), c = i, cv0 = UNHEX(HEX(i)), j = 1;
			WHILE j <= s2_len DO
				SET c = c + 1;
				IF s1_char = SUBSTRING(s2, j, 1) THEN
					SET cost = 0; ELSE SET cost = 1;
				END IF;
				SET c_temp = CONV(HEX(SUBSTRING(cv1, j, 1)), 16, 10) + cost;
				IF c > c_temp THEN SET c = c_temp; END IF;
					SET c_temp = CONV(HEX(SUBSTRING(cv1, j+1, 1)), 16, 10) + 1;
					IF c > c_temp THEN
						SET c = c_temp;
					END IF;
					SET cv0 = CONCAT(cv0, UNHEX(HEX(c))), j = j + 1;
			END WHILE;
			SET cv1 = cv0, i = i + 1;
		END WHILE;
	END IF;
	RETURN c;
END //



-- Returns the Ratio of the levenshtein-Distance between two string
DROP FUNCTION IF EXISTS levenshtein_ratio //
CREATE FUNCTION `levenshtein_ratio`( s1 text, s2 text ) RETURNS int(11)
	DETERMINISTIC
BEGIN
	DECLARE s1_len, s2_len, max_len INT;
	SET s1_len = LENGTH(s1), s2_len = LENGTH(s2);
	IF s1_len > s2_len THEN
		SET max_len = s1_len;
	ELSE
		SET max_len = s2_len;
	END IF;
	RETURN ROUND((1 - LEVENSHTEIN(s1, s2) / max_len) * 100);
END //



-- Allows the Logger to add a Log
DROP PROCEDURE IF EXISTS loggerAddLog //
CREATE PROCEDURE loggerAddLog(
	IN message text,
	IN categoryName varchar(255),
	IN severityName varchar(255),
	IN additionalData text
	)
BEGIN
	DECLARE categoryId int(11);
	DECLARE severityId int(11);

	SELECT ID FROM LogCategories WHERE name = categoryName INTO categoryId;
	SELECT ID FROM LogSeverities WHERE name = severityName INTO severityId;

	IF severityId IS NULL THEN
		INSERT INTO LogSeverities (name) VALUES (severityName);
		SELECT LAST_INSERT_ID() INTO severityId;
	END IF;

	IF categoryId IS NULL THEN
		INSERT INTO LogCategories (name) VALUES (categoryName);
		SELECT LAST_INSERT_ID() INTO categoryId;
	END IF;

	INSERT INTO Logs (
			`message`, `categoryId`, `severityId`, `date`, `additionalData`)
		VALUES (
			message, categoryId, severityId, NOW(), additionalData);
END //



DELIMITER ;
