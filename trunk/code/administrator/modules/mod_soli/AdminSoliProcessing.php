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
				'ERR_USER_NO_SOLI' => 'Es konnten keine Soli-Benutzer gefunden werden',
				'ERR_ADD_COUPON' => 'Ein Fehler ist beim Hinzufügen eines Coupons aufgetreten',
				'ERR_INP' => 'Ein falscher Wert wurde eingegeben', 'COUPON_ADDED' => 'Der Coupon wurde hinzugefügt',);
	}

	function AddCoupon($beg_date, $end_date, $uid) {

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
				$this->soliInterface->ShowError($this->msg['ERR_USER_NO_SOLI']);
			}
			$this->soliInterface->AddCoupon($solis_arr);
		}
	}

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

	function ShowUsers() {

		require_once PATH_INCLUDE . '/user_access.php';

		$userManager = new UserManager();
		try {
			$soli_user = $userManager->getAllSoli();
		} catch (MySQLVoidDataException $e) {
			$this->soliInterface->ShowError($this->msg['ERRUSER_NO_SOLI']);
		} catch (Exception $e) {
			$this->soliInterface->ShowError($this->msg['ERR_SQL'].':'.$e->getMessage());
		}

		$this->soliInterface->ShowSoliUser($soli_user);
	}
	/*
	function ShowSoliOrders() {
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
	            $priceclass = $pcManager->searchEntry(
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
	}*/

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