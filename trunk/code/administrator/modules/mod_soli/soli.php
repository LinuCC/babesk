<?php
defined('_AEXEC') or die('Access denied');

require_once 'AdminSoliInterface.php';
require_once 'AdminSoliProcessing.php';

$soliProcessing = new AdminSoliProcessing();
$soliInterface = new AdminSoliInterface();

if ('POST' == $_SERVER['REQUEST_METHOD']) {
	$action = $_GET['action'];
	switch ($action) {
	case 1: //add coupon
		if (isset($_POST['UID']) && isset($_POST['StartDateYear']))
			$soliProcessing->AddCoupon(
					$_POST['StartDateYear'] . '-' . $_POST['StartDateMonth'] . '-' . $_POST['StartDateDay'],
					$_POST['EndDateYear'] . '-' . $_POST['EndDateMonth'] . '-' . $_POST['EndDateDay'], $_POST['UID']);
		else
			$soliProcessing->AddCoupon(NULL, NULL, NULL);
		break;
	case 2: //show coupons
		$soliProcessing->ShowCoupons();
		break;
	case 3://show Soliusers
		$soliProcessing->ShowUsers();
		break;
	}

} else
	$soliInterface->ShowInitialMenu();

//$soliProcessing->ShowSoliOrders();
?>