SELECT 0 INTO @modId;
CALL moduleGetByPath('ClassTeacher', 'root/administrator/Kuwasys/ClassTeacher', @modId);
DELETE FROM Modules WHERE ID = @modId;
SELECT 0 INTO @modId;
CALL moduleGetByPath('Users', 'root/administrator/Kuwasys/Users', @modId);
DELETE FROM Modules WHERE ID = @modId;