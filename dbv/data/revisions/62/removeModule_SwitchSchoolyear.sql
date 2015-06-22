SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'Upload',
	'root/administrator/System/Schoolyear/SwitchSchoolyear/Upload',
	@moduleId
);
DELETE FROM SystemModules WHERE ID = @moduleId;

SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'ChooseSchoolyear',
	'root/administrator/System/Schoolyear/SwitchSchoolyear/ChooseSchoolyear',
	@moduleId
);
DELETE FROM SystemModules WHERE ID = @moduleId;

SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'SwitchSchoolyear',
	'root/administrator/System/Schoolyear/SwitchSchoolyear',
	@moduleId
);
DELETE FROM SystemModules WHERE ID = @moduleId;