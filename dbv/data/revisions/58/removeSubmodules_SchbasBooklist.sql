-- Remove unused modules

SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'EditBook',
	'root/administrator/Schbas/Booklist/EditBook',
	@moduleId
);
CALL moduleDelete(@moduleId);

SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'DeleteBook',
	'root/administrator/Schbas/Booklist/DeleteBook',
	@moduleId
);
CALL moduleDelete(@moduleId);

SELECT 0 INTO @moduleId;
CALL moduleGetByPath(
	'CreateBook',
	'root/administrator/Schbas/Booklist/CreateBook',
	@moduleId
);
CALL moduleDelete(@moduleId);