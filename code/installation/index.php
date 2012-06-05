<?php

require_once 'InstallationManager.php';

$installationManager = new InstallationManager();

if(isset($_GET['module'])) {
	$installationManager->executeComponentInstallation($_GET['module']);
}
else {
	$installationManager->ShowMenu();
}


?>