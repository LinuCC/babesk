<?php

require_once PATH_INCLUDE . '/Module.php';

class SchbasAccounting extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//no direct access
		defined('_AEXEC') or die("Access denied");

		require_once 'SchbasAccountingInterface.php';

		$SchbasAccountingInterface = new SchbasAccountingInterface($this->relPath);
		if(isset($_GET['action'])) {
			switch($_GET['action']) {

				case 'userSetReturnedFormByBarcode':
					$SchbasAccountingInterface->Scan();
					break;
				case 'userSetReturnedFormByBarcodeAjax':
					$this->userSetReturnedFormByBarcodeAjax();
					break;
				case 'userSetReturnedMsgByButtonAjax':
					$this->userSetReturnedMsgByButtonAjax();
					break;
				default:
					die('Wrong action-value given');
						
					break;
			}
		}
		else {
			$SchbasAccountingInterface->MainMenu();
		}
	}

	/**
	 * based on the post-values given from Ajax, this function sets the
	 * has-user-returned-the-message-value to "hasReturned"
	 *
	 * @return void
	 */
	protected function userSetReturnedFormByBarcodeAjax() {

		$barcode = TableMng::getDb()->real_escape_string($_POST['barcode']);
		$barcodeArray = explode(' ', $barcode);

		if(count($barcodeArray) == 2) {

			$uid = $barcodeArray[0];
			$loanChoice = $barcodeArray[1];
			$haystack = array('nl','ln','lr','ls');

			$query = sprintf("SELECT COUNT(*) FROM schbas_accounting WHERE `UID`='%s'",$uid);
			$result=TableMng::query($query,true);
			if ($result[0]['COUNT(*)']!="0") {
				die('dupe');
			}
			if(is_numeric($uid) && in_array($loanChoice, $haystack,$true)) {
				try {
					$query = sprintf("INSERT INTO schbas_accounting (`UID`,`loanChoice`,`hasPayed`,`payedAmount`) VALUES ('%s','%s','%s','%s')",$uid,$loanChoice,"0","0.00");

					TableMng::query($query,true);
				} catch (Exception $e) {
				}
			}
			else {
				die('notValid');
			}
		}
		else {
			die('error');
		}
	}

}

?>