<?php
require_once PATH_INCLUDE . '/global_settings_access.php';
error_reporting(E_ALL);
global $smarty;
global $logger;

require_once PATH_INCLUDE.'/soli_coupons_access.php';
require_once PATH_INCLUDE.'/soli_order_access.php';
require_once PATH_INCLUDE.'/meal_access.php';

$orderManager = new OrderManager('orders');
$priceClassManager = new PriceClassManager();
$gbManager = new GlobalSettingsManager();
$userManager = new UserManager();
$soliCouponManager = new SoliCouponsManager();
$soliOrderManager = new SoliOrderManager();
$mealManager = new MealManager();

try {
	$orderData = $orderManager->getEntryData($_GET['id'], 'MID');
	$mid = $orderData['MID'];
	$price = $priceClassManager->getPrice($_SESSION['uid'], $mid);
} catch (Exception $e) {
	$logger->log('WEB|mod_cancel', 'MODERATE',
				 sprintf('Error at ID %s, canceling MID %s: %s', $_GET['id'], $orderData['MID'], $e->getMessage()));
	die('<p class="error">Ein Fehler ist aufgetreten!</p>');
}

//"repay", add the price for the menu to the users account
try {
	if ($soliCouponManager->HasValidCoupon($_SESSION['uid'], $mealManager->getEntryValue($mid, 'date'))) {
		if (!$userManager->changeBalance($_SESSION['uid'], $gbManager->getSoliPrice())) {
			$smarty->display("web/modules/mod_cancel/failed.tpl");
			die();
		}
		//The ID of the soli_order is the same as the ID of the order
		$soliOrderManager->delEntry($_GET['id']);
		
	} else {
		if (!$userManager->changeBalance($_SESSION['uid'], $priceClassManager->getPrice($_SESSION['uid'], $mid))) {
			$smarty->display("web/modules/mod_cancel/failed.tpl");
			die();
		}
	}
} catch (Exception $e) {
	$logger->log('WEB|mod_cancel', 'MODERATE',
				 sprintf('Error at ID %s, canceling MID %s: %s', $_GET['id'], $orderData['MID'], $e->getMessage()));
	die('<p class="error">Ein Fehler ist aufgetreten!</p>');
}
$orderManager->delEntry($_GET['id']);
$smarty->display("web/modules/mod_cancel/cancel.tpl");
?>