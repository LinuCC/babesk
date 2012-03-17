<?php 
	define('ERR_INP_MAX_CREDIT','falsche Eingabe des maximalen Guthabens');
	define('ERR_INP_GROUP_NAME','falsche Eingabe des Gruppennamen');
	define('ERR_INP_ID', 'falsche Eingabe der ID');
	define('ERR_INP_PRICE', 'falsche Eingabe des Preises');
	define('ERR_DEL_GROUP', 'konnte die Gruppe nicht löschen');
	define('ERR_DEL_PC', 'konnte die zur Gruppe zugehörigen Preisklassen nicht löschen. Möglicherweise sind einige Datenbankeinträge nun fehlerhaft!');
	define('ERR_ADD_GROUP', 'konnte die Gruppe nicht hinzufügen');
	define('ERR_GET_DATA_GROUP', 'Fehler beim fetchen der Gruppendaten aus dem MySQL-Server');
	define('ERR_CHANGE_GROUP', 'Fehler beim ändern der Gruppendaten');
	define('ERR_GROUP_EXISTING', 'Die Gruppe ist schon vorhanden');
	define('ERR_FETCH_PC', 'Ein Fehler ist beim holen der Preisklassen aufgetreten');
	define('ERR_ADD_PC', 'Konnte eine Preisklasse nicht hinzufügen. Möglicherweise ist der Gruppeneintrag in der Datenbank nun fehlerhaft!');
	
	define('GROUP_DELETED', 'Gruppe erfolgreich gelöscht');
	
	define('GROUP_SMARTY_PARENT', PATH_SMARTY_ADMIN_MOD.'/mod_groups/groups_header.tpl');
	

?>