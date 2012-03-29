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
				'ERR_SQL_SOLIPRICE' => 'Der Tabelleneintrag zum SoliPreis ist nicht vorhanden oder falsch',
				'ERR_SQL_PRICECLASS' => 'Ein Fehler ist beim laden der Preisklassen entstanden',
				'ERR_USER_NO_SOLI' => 'Der angegebene Benutzer ist nicht Soli-berechtigt',
				'ERR_USER_NO_SOLI_FOUND' => 'Es konnten keine Soli-Benutzer gefunden werden',
				'ERR_ADD_COUPON' => 'Ein Fehler ist beim Hinzufügen eines Coupons aufgetreten',
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
		
		require_once PATH_INCLUDE.'/user_access.php';
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
			if(!$userManager->isSoli($uid))//is the user soli?
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
	        	$this->soliInterface->ShowError($this->msg['ERR_SQL_SOLIPRICE'].':'.$e->getMessage());
	        }
	        try {
	            $priceclass = $pcManager->searchEntry(
	                    sprintf('pc_ID = "%s" AND GID = "%s"',
	                            $mealManager->getEntryValue($soli_order['MID'], 'price_class'),
	                            $userManager->getEntryValue($soli_order['UID'], 'GID')));
	            $bank_price = $priceclass['price'] - $soli_price;
	        } catch (Exception $e) {
	        	$this->soliInterface->ShowError($this->msg['ERR_SQL_PRICECLASS'].':'.$e->getMessage());
	        }
	
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