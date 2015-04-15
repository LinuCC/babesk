-- Gnissel
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Gnissel',
	'root/administrator/Gnissel',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "administrator/Gnissel/Gnissel.php"
	WHERE `ID` = @moduleId;

-- GDelUser
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'GDelUser',
	'root/administrator/Gnissel/GDelUser',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Gnissel/GDelUser/GDelUser.php"
	WHERE `ID` = @moduleId;

-- GChangeCard
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'GChangeCard',
	'root/administrator/Gnissel/GChangeCard',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Gnissel/GChangeCard/GChangeCard.php"
	WHERE `ID` = @moduleId;

-- GUnlockUser
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'GUnlockUser',
	'root/administrator/Gnissel/GUnlockUser',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Gnissel/GUnlockUser/GUnlockUser.php"
	WHERE `ID` = @moduleId;

-- GChangePassword
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'GChangePassword',
	'root/administrator/Gnissel/GChangePassword',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Gnissel/GChangePassword/GChangePassword.php"
	WHERE `ID` = @moduleId;

-- GCardInfo
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'GCardInfo',
	'root/administrator/Gnissel/GCardInfo',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Gnissel/GCardInfo/GCardInfo.php"
	WHERE `ID` = @moduleId;