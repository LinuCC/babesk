<?php

require_once 'Web.php';

$smarty;

$webManager = new Web();

if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
	$webManager->logOut();
}
$smarty = $webManager->getSmarty();

$webManager->mainRoutine();

die();

?>
