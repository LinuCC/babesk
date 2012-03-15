<?php
require_once 'AdminSoliInterface.php';
require_once 'AdminSoliProcessing.php';

$soliProcessing = new AdminSoliProcessing();
$soliInterface = new AdminSoliInterface();

$soliProcessing->ShowSoliOrders();
?>