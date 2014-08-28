CALL moduleGetByPath(
	'RechargeCard',
	'root/administrator/Babesk/Recharge/RechargeCard',
	@moduleId
);

UPDATE `SystemModules`
	SET executablePath = "administrator/headmod_Babesk/modules/mod_Recharge/RechargeCard/RechargeCard.php"
	WHERE ID = @moduleId;