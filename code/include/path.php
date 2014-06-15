<?php

//defines the path's for the application

//cut off the filename and go up one directory to the base directory
$rootPath = dirname(dirname(__FILE__));

defined('DS') or define('DS', DIRECTORY_SEPARATOR); //smarty defines DS the same way
//realPath() makes sure we use the right seperator for the platform
defined('PATH_CODE') or define('PATH_CODE', $rootPath);
define('PATH_ADMIN', realPath($rootPath."/administrator"));
define('PATH_PUBLICDATA', realPath($rootPath."/publicData"));
define('PATH_WEB', realPath($rootPath."/web"));
define('PATH_INCLUDE', realPath($rootPath."/include"));
define('PATH_ACCESS', realPath($rootPath."/include/sql_access"));
define('PATH_3RD_PARTY', PATH_INCLUDE . '/3rdParty');

//Smarty
define('PATH_SMARTY', PATH_3RD_PARTY . '/smarty');
define('PATH_SMARTY_TPL', realPath($rootPath . '/smarty_templates'));

//Document root
define('PATH_WEBROOT', $_SERVER['DOCUMENT_ROOT']);

?>
