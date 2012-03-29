<?php

require_once 'AdminSoliInterface.php';
require_once PATH_INCLUDE . '/soli_coupons_access.php';
require_once PATH_INCLUDE . '/soli_order_access.php';

class AdminSoliProcessing {

	function __construct() {
		$this->soliInterface = new AdminSoliInterface();
		$this->soliCouponManager = new SoliCouponsManager();
		$this->soliOrderManager = new SoliOrderManager();
		$this->msg = array('ERR_SQL_NO_DATA' => 'fehlerhafter ID-Eintrag',
				'ERR_SQL' => 'Ein Fehler ist bei der Verbindung zum SQL-Server aufgetreten',
				'ERR_SQL_NO_ORDERS' => 'Es wurden keine Bestellungen aufgegeben',
				'ERR_USER_NO_SOLI' => 'Der angegebene Benutzer ist nicht Soli-berechtigt',
				'ERR_USER_NO_SOLI_FOUND' => 'Es konnten keine Soli-Benutzer gefunden werden',
				'ERR_ADD_COUPON' => 'Ein Fehler ist beim Hinzufügen eines Coupons aufgetreten',
				'ERR_DEL_COUPON' => 'Ein Fehler ist beim löschen des Coupons aufgetreten',
				'FIN_DEL_COUPON' => 'Der Coupon wurde erfolgreich gelöscht',
				'ERR_FETCH_COUPON_INF' => 'Ein Fehler ist beim abholen der Daten aufgetreten',
				'ERR_INP' => 'Ein falscher Wert wurde eingegeben', 'COUPON_ADDED' => 'Der Coupon wurde hinzugefügt',);
	}

	/**
	 * Adds a Coupon
	 * This function adds a coupon to the coupon-table of the MySQL-Server if the Parameters are not NULL.
	 * If one or more of the parameters are NULL, AddCoupon just shows an add-Coupon-form to the user
	 * @param string $beg_date format: YYYY-MM-DD
	 * @param string $end_date format: YYYY-MM-DD
	 * @param numeric_string $uid The ID of the User to whom the card belongs to
	 */
	function AddCoupon($beg_date, $end_date, $uid) {

		require_once PATH_INCLUDE . '/user_access.php';
		$userManager = new UserManager();

		if ($beg_date && $end_date && $uid) {
			/*
			 * add Coupon to table
			 */
			try {
				//check input if there are any errors
				inputcheck($beg_date, 'birthday', 'Datum');
				inputcheck($end_date, 'birthday', 'Datum');
				inputcheck($uid, 'id', 'BenutzerID');
			} catch (WrongInputException $e) {
				$this->soliInterface->ShowError($this->msg['ERR_INP'] . ': ' . $e->getFieldName());
			}
			if (!$userManager->isSoli($uid))//is the user soli?
				$this->soliInterface->ShowError($this->msg['ERR_USER_NO_SOLI']);
			try {
				$this->soliCouponManager->addCoupon($beg_date, $end_date, $uid);
			} catch (Exception $e) {
				$this->soliInterface->ShowError($this->msg['ERR_ADD_COUPON']);
			}
			$this->soliInterface->ShowMsg($this->msg['COUPON_ADDED']);
		} else {
			/*
			 *Show Add-Coupon-Interface
			 */
			require_once PATH_INCLUDE . '/user_access.php';

			$userManager = new UserManager();

			try {
				$solis_arr = $userManager->getAllSoli();
			} catch (MySQLVoidDataException $e) {
				$this->soliInterface->ShowError($this->msg['ERR_USER_NO_SOLI_FOUND']);
			}
			$this->soliInterface->AddCoupon($solis_arr);
		}
	}

	/**
	 * Shows the Coupons
	 * ShowCoupons() shows all SoliCoupons that are existing in the coupon-table to the user
	 */
	function ShowCoupons() {

		require_once PATH_INCLUDE . '/user_access.php';

		$coupons = $this->soliCouponManager->getAllCoupons();
		$userManager = new UserManager();

		foreach ($coupons as &$coupon) {
			$coupon['username'] = $userManager->getForename(($coupon['UID'])) . '.'
					. $userManager->getName(($coupon['UID']));
			$coupon['startdate'] = formatDate($coupon['startdate']);
			$coupon['enddate'] = formatDate($coupon['enddate']);
		}

		$this->soliInterface->ShowCoupon($coupons);
	}

	/**
	 * Deletes a coupon with the ID $id if confirmed is true or shows a Confirmation-Dialog
	 * @param numeric_string $id The ID of the Coupon to delete
	 * @param boolean $confirmed Shows a confirmation-dialog if set to false
	 */
	function DeleteCoupon($id, $confirmed) {
		if ($confirmed) {

			//delete the Coupon
			try {
				$this->soliCouponManager->delEntry($id);
			} catch (Exception $e) {
				$this->soliInterface->ShowError($this->msg['ERR_DEL_COUPON']);
			}
			$this->soliInterface->ShowMsg($this->msg['FIN_DEL_COUPON']);
		} else {

			//Show Confirmation-Dialog
			require_once PATH_INCLUDE . '/user_access.php';
			try {
				$userManager = new UserManager();
				$uid = $this->soliCouponManager->getEntryValue($id, 'UID');
				$username = $userManager->getForename($uid) . '.' . $userManager->getName($uid);
			} catch (Exception $e) {
				$this->soliInterface->ShowError(ERR_FETCH_COUPON_INF);
			}
			$this->soliInterface->ConfirmDelCoupon($id, $username);
		}
	}

	/**
	 * 
	 * Enter description here ...
	 */
	function ShowUsers() {

		require_once PATH_INCLUDE . '/user_access.php';

		$userManager = new UserManager();
		try {
			$soli_user = $userManager->getAllSoli();
		} catch (MySQLVoidDataException $e) {
			$this->soliInterface->ShowError($this->msg['ERRUSER_NO_SOLI']);
		} catch (Exception $e) {
			$this->soliInterface->ShowError($this->msg['ERR_SQL'] . ':' . $e->getMessage());
		}

		$this->soliInterface->ShowSoliUser($soli_user);
	}

	/**
	 * 
	 * Enter description here ...
	 */
	function ShowAllSoliOrders() {
		require_once PATH_INCLUDE . '/user_access.php';
		require_once PATH_INCLUDE . '/meal_access.php';
		require_once PATH_INCLUDE . '/global_settings_access.php';
		require_once PATH_INCLUDE . '/price_class_access.php';

		$soliOrderManager = $this->soliOrderManager;
		$soliCouponManager = $this->soliCouponManager;
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

			$mealname = $soli_order['mealname'];
			$date = formatDate($soli_order['date']);

			$soli_price = $soli_order['soliprice'];
			$bank_price = $soli_order['mealprice'] - $soli_price;

			try {//coupons
				$coupon = $soliCouponManager->searchEntry('UID = ' . $soli_order['UID']);
				$coupon_beg_date = $coupon['startdate'];
				$coupon_end_date = $coupon['enddate'];
			} catch (MySQLVoidDataException $e) {
				//no coupon for the user is existing, dont make the field spitting errors
				$coupon_beg_date = '---';
				$coupon_end_date = '---';
			}
			$orders[] = array('username' => $username, 'mealname' => $mealname, 'date' => $date,
					'coup_begin' => $coupon_beg_date, 'coup_end' => $coupon_end_date, 'soli_price' => $soli_price,
					'bank_price' => $bank_price);
		}
	}

	/**
	 * Shows the Orders which are ordered by User $uid in week $weeknum
	 * Enter description here ...
	 * @param $weeknum the number of the week in the year
	 * @param $uid the UserID of the soli-user who ordered meals
	 */
	function ShowSoliOrdersByDate($weeknum, $uid) {
		if ($weeknum && $uid) {
			$orders = array();
			$monday = getFirstDayOfWeek(date('Y'), $weeknum);
			for ($i = 0; $i < 5; $i++) {
				$buffer = array();
				try {
					$buffer = $this->soliOrderManager->getOrdersByUserandMealdate($uid,
																				  date('Y-m-d', $monday + ($i * 86400)));
				} catch (MySQLVoidDataException $e) {

				}
				foreach($buffer as $order)
					$orders [] = $order;
			}
			var_dump($orders);
			die();
		} else {
			//Show Form to fill out Weeknumber and Soli
			require_once PATH_INCLUDE . '/user_access.php';
			$userManager = new UserManager();
			$solis = $userManager->getAllSoli();
			$this->soliInterface->AskShowSoliUser($solis);
		}

	}

	/**
	 * Object of class AdminSoliInterface
	 * @var AdminSoliInterface
	 */
	protected $soliInterface;

	protected $soliCouponManager;

	protected $soliOrderManager;

	/**
	 * $msg contains messages shown to the User 
	 * @var array($string)
	 */
	protected $msg = array();
}


?>