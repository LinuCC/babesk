<?php

require_once PATH_INCLUDE . '/Module.php';
require_once 'CopyOldOrdersToSoli.php';

class Soli extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute ($dataContainer) {

		defined('_AEXEC') or die('Access denied');
		require_once 'AdminSoliInterface.php';
		require_once 'AdminSoliProcessing.php';

		$soliInterface = new AdminSoliInterface($this->relPath);
		$soliProcessing = new AdminSoliProcessing($soliInterface);

		if (('POST' == $_SERVER['REQUEST_METHOD']) && isset($_GET['action'])) {
			$action = $_GET['action'];
			switch ($action) {
				case 1: //add coupon
					if (isset($_POST['UID']) && isset($_POST['StartDateYear']))
						$soliProcessing->AddCoupon($_POST['StartDateYear'] . '-' . $_POST['StartDateMonth'] . '-' .
							$_POST['StartDateDay'], $_POST['EndDateYear'] . '-' . $_POST['EndDateMonth'] . '-' . $_POST[
							'EndDateDay'], $_POST['UID']);
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
					if (isset($_POST['user_id']))
						$soliProcessing->ChangeSettings($_POST['soli_price']);
					else
						$soliProcessing->ChangeSettings(NULL);
					break;
				case 7: //copy old orders to soli
					if (isset($_POST['copy'])) {
						// $soliProcessing->CopyOldOrdersToSoli();
						CopyOldOrdersToSoli::init($soliInterface);
						CopyOldOrdersToSoli::execute();
					}
					else if (isset($_POST['dont_copy']))
						$soliInterface->ShowInitialMenu();
					else
						$soliInterface->AskCopyOldOrdersToSoli();
					break;
			}

		}
		else
			$soliInterface->ShowInitialMenu();
	}
}

//$soliProcessing->ShowSoliOrders();
?>