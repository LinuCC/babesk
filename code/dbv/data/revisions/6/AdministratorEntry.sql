INSERT INTO `users` (ID, forename, name, username, password) VALUES
	-- Default Password of administrator is root
	(1, 'admin', 'admin', 'administrator', '63a9f0ea7bb98050796b649e85481845');

INSERT INTO `UserInGroups` (userId, groupId) VALUES
	(1, 3);