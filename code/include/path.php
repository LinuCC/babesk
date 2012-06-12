<?php
    //defines the path's for the application
    
    //cut off the filename and go up one directory to the base directory
    $rootPath = dirname(dirname(__FILE__));

    defined('DS') or define('DS', DIRECTORY_SEPARATOR); //smarty defines DS the same way
    //realPath() makes sure we use the right seperator for the platform
    define('PATH_SITE', $rootPath);
    define('PATH_ADMIN', realPath($rootPath."/administrator"));
    define('PATH_WEB', realPath($rootPath."/web"));
    define('PATH_INCLUDE', realPath($rootPath."/include"));
    define('PATH_ACCESS', realPath($rootPath."/include/sql_access"));
	define('PATH_SMARTY', realPath($rootPath."/smarty"));
	define('PATH_SMARTY_ADMIN_TEMPLATES', realPath($rootPath."/smarty/templates/administrator"));
	define('PATH_SMARTY_INH_PARENT', realPath($rootPath.'/smarty/templates/administrator/base_layout.tpl'));
	//define('PATH_SMARTY_ADMIN_MOD', realPath($rootPath.'/smarty/templates/administrator/modules'));
	
	define('PATH_WEBROOT', $_SERVER['DOCUMENT_ROOT']);
	
	$smartypath = $rootPath."/smarty";     //I <3 verschiedene Ordnertrennzeichen der OS's etc.
	$smartypath = str_replace(realPath(PATH_WEBROOT).DS, '', $smartypath);
	
	define('REL_PATH_SMARTY', '/'.$smartypath);    //the relative path to /smarty starting at the webroot 

?>