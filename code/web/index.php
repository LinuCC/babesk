<?php

require_once 'Web.php';

$smarty;

$webManager = new Web();

if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
	$webManager->logOut();
}
$smarty = $webManager->getSmarty();

if (isset($_GET['section'])) {
	$webManager->mainRoutine($_GET['section']);
}
else {
	$webManager->mainRoutine(false);
}

die();

?>