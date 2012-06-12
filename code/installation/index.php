<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("default_charset", "utf-8");
//if this value is not set, the modules will not execute
define('_AEXEC', 1);

define('PATH_CODE', realpath(__DIR__ . '/..'));

require_once PATH_CODE . '/include/path.php';
require_once 'InstallationManager.php';

$installationManager = new InstallationManager();

if(isset($_GET['module'])) {
	$installationManager->executeComponentInstallation($_GET['module']);
}
else {
	$installationManager->ShowMenu();
}


?>