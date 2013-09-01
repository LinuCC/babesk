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
				case 'remember':
					$this->remember();
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

					$grade = TableMng::query(sprintf("SELECT g.gradelevel FROM jointusersingrade as juig, Grades as g WHERE juig.GradeID=g.ID and juig.UserID='%s'",$uid));

					if ($loanChoice=="ln")	$amountToPay = TableMng::query(sprintf("SELECT fee_normal as fee FROM schbas_fee WHERE grade='%s'",$grade[0]['gradelevel']+1));
					if ($loanChoice=="lr")	$amountToPay = TableMng::query(sprintf("SELECT fee_reduced as fee FROM schbas_fee WHERE grade='%s'",$grade[0]['gradelevel']+1));
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
		$schoolyearDesired = TableMng::query('SELECT ID FROM schoolYear WHERE active = 1');
		$schoolyearID = $schoolyearDesired[0]['ID'];
		$gradeID = TableMng::query("SELECT DISTINCT gradeID FROM usersInGradesAndSchoolyears WHERE schoolyearID = $schoolyearID");
		foreach ($gradeID as $grade){
			$ID = $grade['gradeID'];
			$SaveTheCows = TableMng::query("SELECT * FROM Grades WHERE ID = $ID");
			// Cows stands for Code of worst systematic
			$gradesAll[] = $SaveTheCows[0];
		}
		$users = TableMng::query('SELECT * FROM users ORDER BY name ASC');
		$users = $this->addGradeLabelToUsers($users);
		$users = $this->addPayedAmountToUsers($users);
		if (isset ($_GET['gradeIdDesired'])){
			for ($i=0; $i<sizeof($gradesAll); $i++){
				if ($gradesAll[$i]['gradelevel'].'-'.$gradesAll[$i]['label'] == $_GET['gradeIdDesired']){
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

		$jointsUsersInGrade = TableMng::query('SELECT * FROM usersInGradesAndSchoolyears');
		$grades = TableMng::query('SELECT * FROM Grades');
		if (isset($users) && count ($users) && isset($jointsUsersInGrade) && count ($jointsUsersInGrade)) {
			foreach ($users as & $user) {
				foreach ($jointsUsersInGrade as $joint) {
					if ($joint['userId'] == $user['ID']) {
						foreach ($grades as $grade) {
							if ($grade['ID'] == $joint['gradeId']) {
								$user['gradeLabel'] = $grade['gradelevel'] . '-' . $grade['label'];
							}
						}
					}
				}
			}
		}
		return $users;
	}

	private function addPayedAmountToUsers ($users) {

		$payed = TableMng::query('SELECT * FROM schbas_accounting');
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
	
	private function remember(){
		
		$showIdAfterName = false;				// to enable the id after name set this value to true
		
		$lending = TableMng::query('SELECT * FROM schbas_lending');
		
		echo ("<style type='text/css'>
				th {border: 1px solid #8c8c8c; background-color: d2d2d2; padding: 2px;}
				table {margin: 0 auto; text-align: left;}
				</style>");
		
		echo ('	<table border="0" ');
		
		echo ("<tr><th>Sch&uuml;ler</th><th>Klasse</th><th>Buch</th><th>Ausleihdatum</th></tr>");
		
		for ($i=0; $i< (count($lending)); $i++){	// one loop prodices one line of the table
			echo ("<tr>");
				//name
				$id = (int) $lending[$i]["user_id"];
				$name = TableMng::query("SELECT name FROM users WHERE ID=$id");
				$name = $name[0]["name"];
				$forename = TableMng::query("SELECT forename FROM users WHERE ID=$id");
				$forename = $forename[0]["forename"];
				if($showIdAfterName == true){
					echo ("<th>".$forename." ".$name." (".$id.")</th>");	// output of name
				}else{
					echo ("<th>".$forename." ".$name."</th>");
				}
				
				//class
				try{
					$schoolyearDesired = TableMng::query('SELECT ID FROM schoolYear WHERE active = 1');
					$schoolyearID = $schoolyearDesired[0]['ID'];
					$gradeID = TableMng::query(sprintf("SELECT GradeID FROM usersInGradesAndSchoolyears WHERE UserID = '$id' AND schoolyearID = $schoolyearID"));
					$gradeIDtemp = (int)$gradeID[0]['GradeID'];
					$gradelevel = TableMng::query(sprintf("SELECT gradelevel FROM Grades WHERE ID = $gradeIDtemp"));
					$grade = $gradelevel[0]['gradelevel'];
					$label = TableMng::query(sprintf("SELECT label FROM Grades WHERE ID = $gradeIDtemp"));
					$label = $label[0]['label'];
				}catch (Exception $e){
					$grade = 0;
				}
				$class = "$grade $label";
				
				echo ("<th>$class</th>");
				
				//book
				$bookid = (int) $lending[$i]["inventory_id"];
				$title = TableMng::query("SELECT title FROM schbas_books WHERE id=$bookid");
				$title = $title[0]["title"];
				echo ("<th>".$title."</th>");	// output of booktitle
				
				//date
				$date = $lending[$i]["lend_date"];
				echo ("<th>".$date."</th>");	// output of date
			
			echo ("</tr>");
		}
		
		echo ("</table><br><a href='./index.php?section=Schbas|SchbasAccounting'>Zurück</a>");
	}
}
?>
