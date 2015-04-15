-- Babesk
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Babesk',
	'root/administrator/Babesk',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` = "administrator/Babesk/Babesk.php"
	WHERE `ID` = @moduleId;

-- Soli
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Soli',
	'root/administrator/Babesk/Soli',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Babesk/Soli/Soli.php"
	WHERE `ID` = @moduleId;

-- Recharge
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Recharge',
	'root/administrator/Babesk/Recharge',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Babesk/Recharge/Recharge.php"
	WHERE `ID` = @moduleId;

-- Priceclass
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Priceclass',
	'root/administrator/Babesk/Priceclass',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Babesk/Priceclass/Priceclass.php"
	WHERE `ID` = @moduleId;

-- Menu
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Menu',
	'root/administrator/Babesk/Menu',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Babesk/Menu/Menu.php"
	WHERE `ID` = @moduleId;

-- Meals
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Meals',
	'root/administrator/Babesk/Meals',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Babesk/Meals/Meals.php"
	WHERE `ID` = @moduleId;

-- Checkout
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Checkout',
	'root/administrator/Babesk/Checkout',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Babesk/Checkout/Checkout.php"
	WHERE `ID` = @moduleId;

-- BabeskTimeSettings
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'BabeskTimeSettings',
	'root/administrator/Babesk/BabeskTimeSettings',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Babesk/BabeskTimeSettings/BabeskTimeSettings.php"
	WHERE `ID` = @moduleId;

-- RechargeCard
SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'RechargeCard',
	'root/administrator/Babesk/Recharge/RechargeCard',
	@moduleId
);
UPDATE `SystemModules`
	SET `executablePath` =
		"administrator/Babesk/Recharge/RechargeCard/RechargeCard.php"
	WHERE `ID` = @moduleId;