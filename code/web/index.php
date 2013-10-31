<?php

require_once 'Web.php';

$webManager = new Web();

if (isset($_GET['action']) AND $_GET['action'] == 'logout') {
	$webManager->logOut();
}
$webManager->mainRoutine();

?>
