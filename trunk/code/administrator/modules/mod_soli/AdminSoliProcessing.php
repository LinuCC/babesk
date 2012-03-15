<?php

require_once 'AdminSoliInterface.php';

class AdminSoliProcessing {
	function __construct() {
		$this->soliInterface = new AdminSoliInterface();
		$this->msg = array('ERR_SQL_NO_DATA' => '<p class="error">fehlerhafter ID-Eintrag</p>',
							'ERR_SQL_NO_ORDERS' => 'Es wurden keine Bestellungen aufgegeben',
							'ERR_INP' => 'Ein falscher Wert wurde eingegeben');
	}

	function addSoliOrder($MID, $UID, $IP, $date) {
		require_once PATH_INCLUDE . '/soli_order_access.php';
		if ($MID && $UID && $IP && $date) {
			try {
				inputcheck($MID, 'id', 'Mahlzeit');
				inputcheck($UID, 'id', 'Benutzer');
				inputcheck($IP, 'id', 'IP-Adresse');
			} catch (WrongInputException $e) {
				$this->soliInterface->ShowError($this->msg['ERR_INP'].' in '.$e->getFieldName());
			}
			$soliOrderManager = new SoliOrderManager();
			$soliOrderManager->addSoliOrder($MID, $UID, $IP, $date);
		} else {
			
			
			
			
			
			///FILEFILEFLIEFILEFLIEFLIEF
			
			
			
			

		}
	}

	function ShowSoliOrders() {
		require_once PATH_INCLUDE . '/soli_order_access.php';
		require_once PATH_INCLUDE . '/soli_coupons_access.php';
		require_once PATH_INCLUDE . '/user_access.php';
		require_once PATH_INCLUDE . '/meal_access.php';
		require_once PATH_INCLUDE . '/global_settings_access.php';
		require_once PATH_INCLUDE . '/price_class_access.php';

		$soliOrderManager = new SoliOrderManager();
		$soliCouponManager = new SoliCouponsManager();
		$gsManager = new GlobalSettingsManager();
		$userManager = new UserManager();
		$mealManager = new MealManager();
		$pcManager = new PriceClassManager();

		try {
			$soli_orders = $soliOrderManager->getSortedOrders();
		} catch (MySQLVoidDataException $e) {
			$this->soliInterface->ShowError($this->msg['ERR_SQL_NO_ORDERS']);
		}

		$orders = array();

		foreach ($soli_orders as $soli_order) {

			if (!$soli_order)
				continue;

			//ID of order itself
			$id = $soli_order['ID'];

			try { //Username
				$username = sprintf('%s %s', $userManager->getEntryValue($id, 'forename'),
						$userManager->getEntryValue($soli_order['UID'], 'name'));
			} catch (MySQLVoidDataException $e) {
				$username = $this->msg['ERR_SQL_NO_DATA'];
			}

			try {//Mealname
				$mealname = $mealManager->getEntryValue($soli_order['MID'], 'name');
			} catch (Exception $e) {
				$mealname = $this->msg['ERR_SQL_NO_DATA'];
			}
			$date = formatDate($soli_order['date']);

			try {
				$soli_price = $gsManager->getSoliPrice();
			} catch (Exception $e) {
			}
			try {
				$priceclass = $pcManager
						->searchEntry(
								sprintf('pc_ID = "%s" AND GID = "%s"',
										$mealManager->getEntryValue($soli_order['MID'], 'price_class'),
										$userManager->getEntryValue($soli_order['UID'], 'GID')));
				$bank_price = $priceclass['price'] - $soli_price;
			} catch (Exception $e) {
			}

			try {//coupons
				$coupon = $soliCouponManager->searchEntry('UID = ' . $soli_order['UID']);
				$coupon_beg_date = $coupon['startdate'];
				$coupon_end_date = $coupon['enddate'];
			} catch (MySQLVoidDataException $e) {
			}
			$orders[] = array('username' => $username, 'mealname' => $mealname, 'date' => $date,
					'coup_begin' => $coupon_beg_date, 'coup_end' => $coupon_end_date, 'soli_price' => $soli_price,
					'bank_price' => $bank_price);
		}
	}
	
	protected $soliInterface;
	protected $msg = array();
}

?>