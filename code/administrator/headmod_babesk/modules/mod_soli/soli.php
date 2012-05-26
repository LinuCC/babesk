<?php
defined('_AEXEC') or die('Access denied');

require_once 'AdminSoliInterface.php';
require_once 'AdminSoliProcessing.php';

$soliProcessing = new AdminSoliProcessing();
$soliInterface = new AdminSoliInterface();

if (('POST' == $_SERVER['REQUEST_METHOD']) && isset($_GET['action'])) {
	$action = $_GET['action'];
	switch ($action) {
		case 1: //add coupon
			if (isset($_POST['UID']) && isset($_POST['StartDateYear']))
				$soliProcessing->AddCoupon(
						$_POST['StartDateYear'] . '-' . $_POST['StartDateMonth'] . '-' . $_POST['StartDateDay'],
						$_POST['EndDateYear'] . '-' . $_POST['EndDateMonth'] . '-' . $_POST['EndDateDay'],
						$_POST['UID']);
			else
				$soliProcessing->AddCoupon(NULL, NULL, NULL);
			break;
		case 2: //show coupons
			$soliProcessing->ShowCoupons();
			break;
		case 3: //show Soliusers
			$soliProcessing->ShowUsers();
			break;
		case 4: //show SoliOrders for specific User and Week
			if (isset($_POST['ordering_kw']) && isset($_POST['user_id']))
				$soliProcessing->ShowSoliOrdersByDate($_POST['ordering_kw'], $_POST['user_id']);
			else
				$soliProcessing->ShowSoliOrdersByDate(false, false);
			break;
		case 5: //delete coupon
			if (isset($_POST['delete']))
				$soliProcessing->DeleteCoupon($_GET['ID'], true);
			else if (isset($_POST['not_delete']))
				$soliProcessing->ShowCoupons();
			else
				$soliProcessing->DeleteCoupon($_GET['ID'], false);
			break;
		case 6: //Change Soli-Settings
			if (isset($_POST['soli_price']))
				$soliProcessing->ChangeSettings($_POST['soli_price']);
			else
				$soliProcessing->ChangeSettings(NULL);
			break;
	}

} else
	$soliInterface->ShowInitialMenu();

//$soliProcessing->ShowSoliOrders();
?>