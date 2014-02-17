SELECT 0 INTO @modId;
CALL moduleGetByPath('AssignUsersToClasses', 'root/administrator/Kuwasys/Classes/AssignUsersToClasses', @modId);
DELETE FROM Modules WHERE ID = @modId;