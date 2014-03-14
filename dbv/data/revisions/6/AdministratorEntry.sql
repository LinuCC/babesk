INSERT INTO `users` (ID, forename, name, username, password, email, telephone, birthday, last_login, locked, GID, credit, soli) VALUES (
		1, 'admin', 'admin', 'administrator',
		-- Default Password of administrator is root
		'63a9f0ea7bb98050796b649e85481845',
		'', '', '', '', 0, 0, 0.00, 0
	);

INSERT INTO `UserInGroups` (userId, groupId) VALUES (1, 3);