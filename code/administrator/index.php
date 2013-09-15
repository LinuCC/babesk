<?php

require_once 'Administrator.php';
$adminManager = new Administrator();
$smarty = $adminManager->getSmarty();
$adminManager->run();

?>
