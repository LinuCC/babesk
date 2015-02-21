SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Admin',
	'root/administrator/System/Admin',
	@moduleId
);

DELETE FROM SystemModules WHERE ID = @moduleId;