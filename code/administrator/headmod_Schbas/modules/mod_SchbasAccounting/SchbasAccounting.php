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

		$this->SchbasAccountingInterface = new SchbasAccountingInterface($this->relPath);
		if(isset($_GET['action'])) {
			switch($_GET['action']) {

				case 'userSetReturnedFormByBarcode':
					$this->SchbasAccountingInterface->Scan();
					break;
				case 'userSetReturnedFormByBarcodeAjax':
					$this->userSetReturnedFormByBarcodeAjax();
					break;
				case 'userSetReturnedMsgByButtonAjax':
					$this->userSetReturnedMsgByButtonAjax();
					break;
				case 1:
					if (isset($_GET['ajax'])){
						//$this->SchbasAccountingInterface->test();
						$this->executePayment($_POST['ID'], $_POST['payment']);
						break;
					}else{
						$this->showUsers();break;
					}
				default:
					die('Wrong action-value given');
						
					break;
			}
		}
		else {
			$this->SchbasAccountingInterface->MainMenu();
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
					
					$grade = TableMng::query(sprintf("SELECT g.gradeValue FROM jointusersingrade as juig, grade as g WHERE juig.GradeID=g.ID and juig.UserID='%s'",$uid),true);
					
					if ($loanChoice=="ln")	$amountToPay = TableMng::query(sprintf("SELECT fee_normal as fee FROM schbas_fee WHERE grade='%s'",$grade[0]['gradeValue']+1),true);
					if ($loanChoice=="lr")	$amountToPay = TableMng::query(sprintf("SELECT fee_reduced as fee FROM schbas_fee WHERE grade='%s'",$grade[0]['gradeValue']+1),true);
					if (!isset($amountToPay)) $amountToPay[0]['fee']="0.00";
					$query = sprintf("INSERT INTO schbas_accounting (`UID`,`loanChoice`,`payedAmount`,`amountToPay`) VALUES ('%s','%s','%s','%s')",$uid,$loanChoice,"0.00",$amountToPay[0]['fee']);

					TableMng::query($query);
				} catch (Exception $e) {
					var_dump($e->getMessage());
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
	
	private function showUsers () {
		$schoolyearDesired = TableMng::query('SELECT ID FROM schoolYear WHERE active = 1', true);
		$schoolyearID = $schoolyearDesired[0]['ID'];
		$gradeID = TableMng::query("SELECT GradeID FROM jointGradeInSchoolYear WHERE SchoolYearID = $schoolyearID",true);
		foreach ($gradeID as $grade){
			$ID = $grade['GradeID'];
			$SaveTheCows = TableMng::query("SELECT * FROM grade WHERE ID = $ID", true);
			// Cows stands for Code of worst systematic
			$gradesAll[] = $SaveTheCows[0];
		}
		$users = TableMng::query('SELECT * FROM users ORDER BY name ASC', true);
		$users = $this->addGradeLabelToUsers($users);
		$users = $this->addPayedAmountToUsers($users);
		if (isset ($_GET['gradeIdDesired'])){
			for ($i=0; $i<sizeof($gradesAll); $i++){
				if ($gradesAll[$i]['gradeValue'].'-'.$gradesAll[$i]['label'] == $_GET['gradeIdDesired']){
					$gradeDesired = $gradesAll[$i]['ID'];
				}
			}
			$i = 0;
			foreach ($users as &$user) {
				if (isset($user["gradeLabel"])){
					if ($user["gradeLabel"] != $_GET['gradeIdDesired'])
						unset($users[$i]);
				}else{
					unset($users[$i]);
				}
				$i++;
			}
			$users = array_values($users);
		}else{
			$gradeDesired = null;
		}
		$this->SchbasAccountingInterface->showAllUsers($gradesAll,$gradeDesired,$users);
	}
	
	private function addGradeLabelToUsers ($users) {
	
		$jointsUsersInGrade = TableMng::query('SELECT * FROM jointUsersInGrade', true);
		$grades = TableMng::query('SELECT * FROM grade', true);
		if (isset($users) && count ($users) && isset($jointsUsersInGrade) && count ($jointsUsersInGrade)) {
			foreach ($users as & $user) {
				foreach ($jointsUsersInGrade as $joint) {
					if ($joint['UserID'] == $user['ID']) {
						foreach ($grades as $grade) {
							if ($grade['ID'] == $joint['GradeID']) {
								$user['gradeLabel'] = $grade['gradeValue'] . '-' . $grade['label'];
							}
						}
					}
				}
			}
		}
		return $users;
	}
	
	private function addPayedAmountToUsers ($users) {
	
		$payed = TableMng::query('SELECT * FROM schbas_accounting', true);
	//	$fees = TableMng::query('SELECT * FROM schbas_fee', true);
		foreach ($users as & $user) {
			foreach ($payed as $pay) {
				if ($pay['UID'] == $user['ID'])  {		
					$user['payedAmount'] = $pay['payedAmount'];
					$user['amountToPay'] = $pay['amountToPay'];
					$user['loanChoice'] = $pay['loanChoice'];
// 					foreach ($fees as $fee) {
// 						if (isset($user['gradeLabel']) && $fee['grade']==preg_replace("/[^0-9]/", "", $user['gradeLabel'])+1) {
// 							if ($user['loanChoice']=="ln") $user['amountToPay']=$fee['fee_normal'];
// 							if ($user['loanChoice']=="lr") $user['amountToPay']=$fee['fee_reduced'];
// 						}
// 					}
				}
			}
		}
		return $users;
	}

	
	private function executePayment($UID, $payment){
		$UID = str_replace("Payment", "", $UID);
		try {
			TableMng::query("UPDATE schbas_accounting SET payedAmount=$payment WHERE UID=$UID");
			//die("UPDATE schbas_accounting SET payedAmount=$payment WHERE 'UID'=$UID");
		} catch (Exception $e) {
			//die("UPDATE schbas_accounting SET 'payedAmount'=$payment WHERE 'UID'=$UID".$e);
		}
		
	}

}

?>