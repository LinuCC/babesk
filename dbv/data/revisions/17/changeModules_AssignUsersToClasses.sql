SELECT 0 INTO @modId;

CALL moduleGetByPath('AssignUsersToClasses', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/AssignUsersToClasses.php'
	WHERE ID = @modId;

CALL moduleGetByPath('Overview', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/Overview', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/Overview.php'
	WHERE ID = @modId;

CALL moduleGetByPath('Reset', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/Reset', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/Reset.php'
	WHERE ID = @modId;

CALL moduleGetByPath('Classdetails', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/Classdetails', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/Classdetails.php'
	WHERE ID = @modId;

CALL moduleGetByPath('ClassdetailsGet', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/ClassdetailsGet', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/ClassdetailsGet.php'
	WHERE ID = @modId;

CALL moduleGetByPath('ChangeClassOfUser', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/ChangeClassOfUser', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/ChangeClassOfUser.php'
	WHERE ID = @modId;

CALL moduleGetByPath('ChangeStatusOfUser', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/ChangeStatusOfUser', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/ChangeStatusOfUser.php'
	WHERE ID = @modId;

CALL moduleGetByPath('ApplyChanges', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/ApplyChanges', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/ApplyChanges.php'
	WHERE ID = @modId;

CALL moduleGetByPath('AddUserToClass', 'root/administrator/Kuwasys/KuwasysUsers/AssignUsersToClasses/AddUserToClass', @modId);
UPDATE Modules
	SET executablePath = 'administrator/headmod_Kuwasys/modules/mod_KuwasysUsers/AssignUsersToClasses/AddUserToClass.php'
	WHERE ID = @modId;