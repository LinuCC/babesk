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
					$query = sprintf("INSERT INTO schbas_accounting (`UID`,`loanChoice`,`hasPayed`,`payedAmount`) VALUES ('%s','%s','%s','%s')",$uid,$loanChoice,"0","0.00");

					TableMng::query($query);
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
	
	private function showUsers () {
		$schoolyearDesired = TableMng::query('SELECT ID FROM schoolYear WHERE active = 1', true);
		$schoolyearID = $schoolyearDesired[0]['ID'];
		$gradeID = TableMng::query("SELECT GradeID FROM jointgradeinschoolyear WHERE SchoolYearID = $schoolyearID",true);
		foreach ($gradeID as $grade){
			$ID = $grade['GradeID'];
			$SaveTheCows = TableMng::query("SELECT * FROM grade WHERE ID = $ID", true);
			// Cows stands for Code of worst systematic
			$gradesAll[] = $SaveTheCows[0];
		}
		$users = TableMng::query('SELECT * FROM users', true);
		$users = $this->addGradeLabelToUsers($users);
		if (isset ($_GET['gradeIdDesired'])){
			for ($i=0; $i<sizeof($gradesAll); $i++){
				if ($gradesAll[$i]['gradeValue'].'-'.$gradesAll[$i]['label'] == $_GET['gradeIdDesired']){
					$gradeDesired = $gradesAll[$i]['ID'];
				}
			}
			for ($i=0; $i<sizeof($users); $i++){
				if (isset($users[$i]["gradeLabel"])){
					if ($users[$i]["gradeLabel"] != $_GET['gradeIdDesired'])
						unset($users[$i]);
				}else{
					unset($users[$i]);
				}
			}
		}else{
			$gradeDesired = null;
		}
		$this->SchbasAccountingInterface->showAllUsers($gradesAll,$gradeDesired,$users);
	}
	
	private function addGradeLabelToUsers ($users) {
	
		$jointsUsersInGrade = $this->getAllJointsUsersInGrade();
		$grades = $this->getAllGrades();
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
	
	private function getAllJointsUsersInGrade () {
	
		try {
			$joints = TableMng::query('SELECT * FROM jointUsersInGrade', true);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('warningNoJointsUsersInGrade'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointUsersInGrade'));
		}
		if(isset($joints)) {
			return $joints;
		}
	}
	
	private function getAllGrades () {
	
		try {
			$grades = TableMng::query('SELECT * FROM grade', true);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoGrades'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchGrades'));
		}
		return $grades;
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