<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Schbas/Schbas.php';

class SchbasAccounting extends Schbas {

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
		$this->entryPoint($dataContainer);

		require_once 'SchbasAccountingInterface.php';
		require_once PATH_ACCESS.'/LoanManager.php';

		$this->SchbasAccountingInterface = new SchbasAccountingInterface($this->relPath);
		$this->lm = new LoanManager();

		$this->_pdo = $dataContainer->getPdo();




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
				case 'sendReminder':
					$this->sendReminder();
					break;
				case 'deleteAll':
					$this->deleteAll();
					break;
				case 'remember':
					$this->remember();
					break;
				case 'userRemoveByID':
					if (isset($_POST['UserID'])){
						$this->userRemoveByID();

					}else{
						$this->SchbasAccountingInterface->showDelete();

					}

					break;
				case 'remember2':
					$this->remember2();
					break;
				case 'rebmemer2':
					$this->rebmemer2();
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

			$listOfClasses = $this->getListOfClasses();
			$listOfClassesRebmemer = $this->getListOfClasses("rebmemer2");
			$this->SchbasAccountingInterface->MainMenu($listOfClasses, $listOfClassesRebmemer);
		}
	}

	/**
	 * send reminders via the message module to all users who haven't payed the
	 * complete fee yet. this function uses the selected reminder template for usage.
	 */
	protected function sendReminder () {
		try {
			$template= TableMng::query("SELECT mt.title, mt.text FROM MessageTemplate AS mt WHERE mt.ID=(SELECT value FROM SystemGlobalSettings WHERE name='schbasReminderMessageID')");
			$author = TableMng::query("SELECT value FROM SystemGlobalSettings WHERE name='schbasReminderAuthorID'");
			$group = TableMng::query("SELECT ID FROM MessageGroups WHERE name='schbas'");
			TableMng::query("INSERT INTO MessageMessages (`ID`, `title`, `text`, `validFrom`, `validTo`, `originUserId`, `GID`) VALUES (NULL, '".$template[0]['title']."', '".$template [0]['text']."', '".date("Y-m-d")."', '".date("Y-m-d",strtotime("+4 weeks"))."', '".$author[0]['value']."', '".$group[0]['ID']."');");
			$messageID = TableMng::$db->insert_id;
			TableMng::query("INSERT INTO MessageManagers (`ID`, `messageID`, `userId`) VALUES (NULL, '".$messageID."','".$author[0]['value']."')");
			$usersToRemind = TableMng::query("SELECT * FROM SchbasAccounting WHERE payedAmount < amountToPay");
			foreach ($usersToRemind as $user) {
				TableMng::query("INSERT INTO MessageReceivers (`ID`, `messageID`, `userID`, `read`, `return`) VALUES (NULL, '".$messageID."', '".$user['UID']."', '0', 'noReturn');");

			}
		}
		catch (Exception $e) {
		}
		$this->SchbasAccountingInterface->reminderSent();
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

			$query = sprintf("SELECT COUNT(*) FROM SchbasAccounting WHERE `UID`='%s'",$uid);
			$result=TableMng::query($query);
			if ($result[0]['COUNT(*)']!="0") {
				die('dupe');
			}
			//Check if the user is in an active Grade
			$user = $this->_entityManager->find('Babesk:SystemUsers', $uid);
			$count = $this->_entityManager->createQuery(
				'SELECT COUNT(u) FROM Babesk:SystemUsers u
					JOIN u.usersInGradesAndSchoolyears uigs
					JOIN uigs.schoolyear s WITH s.active = 1
					WHERE u.id = :userId
			')->setParameter('userId', $uid)
				->getSingleScalarResult();
			if(!$count) {
				die('noActiveGrade');
			}
			if(is_numeric($uid) && in_array($loanChoice, $haystack, true)) {
				try {

					$loanbooks = array();

					require_once PATH_ACCESS . '/LoanManager.php';
					require_once PATH_INCLUDE . '/Schbas/Loan.php';
					$lm = new LoanManager();
					$loanHelper = new \Babesk\Schbas\Loan(
						$this->_dataContainer
					);
					$loanbooks = $loanHelper->loanBooksGet($uid);
					$loanbooksSelfBuy = TableMng::query("SELECT BID FROM SchbasSelfpayer WHERE UID=".$uid);
					$loanbooksSelfBuy = array_map('current',$loanbooksSelfBuy);

					$checkedBooks = array();
					$feeNormal = 0.00;
					$oneYear = array("05","06","07","08","09","10");
					$twoYears = array(56,67,78,89,"90",12,13);
					$threeYears = array(79,91);
					$fourYears = array(69,92);
					foreach ($loanbooks as $book) {
						if (!in_array($book['id'],$loanbooksSelfBuy)) {
							if(in_array($book['class'],$oneYear)) $feeNormal += $book['price'];
							if(in_array($book['class'],$twoYears)) $feeNormal += $book['price']/2;
							if(in_array($book['class'],$threeYears)) $feeNormal += $book['price']/3;
							if(in_array($book['class'],$fourYears)) $feeNormal += $book['price']/4;
						}
					}


					//get loan fees
					//gesamtausleihpreis dritteln
					$feeNormal /=3;

					//für reduzierten Preis vom gedrittelten preis 20% abziehen
					$feeReduced = $feeNormal * 0.8;
					$feeNormal = number_format( round($feeNormal,0) , 2, ',','.'); //preise auf volle
					$feeReduced = number_format( round($feeReduced,0) , 2, ',','.');//betraege runden
				//	$grade = TableMng::query(sprintf("SELECT g.gradelevel FROM jointusersingrade as juig, SystemGrades as g WHERE juig.GradeID=g.ID and juig.UserID='%s'",$uid));

					if ($loanChoice=="ln")	$amountToPay = $feeNormal;
					if ($loanChoice=="lr")	$amountToPay = $feeReduced;
					if (!isset($amountToPay)) $amountToPay="0.00";
					$query = sprintf(
						"INSERT INTO SchbasAccounting
							(`UID`,`loanChoiceId`,`payedAmount`,`amountToPay`)
							VALUES ('%s',(
									SELECT ID FROM SchbasLoanChoices
										WHERE abbreviation = '%s'
								) ,'%s','%s')"
							,$uid,$loanChoice,"0.00",$amountToPay);

					TableMng::query($query);
					die('success');
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
		$schoolyearDesired = TableMng::query('SELECT ID FROM SystemSchoolyears WHERE active = 1');
		$schoolyearID = $schoolyearDesired[0]['ID'];
		$gradeID = TableMng::query("SELECT DISTINCT gradeID FROM SystemUsersInGradesAndSchoolyears WHERE schoolyearID = $schoolyearID");
		foreach ($gradeID as $grade){
			$ID = $grade['gradeID'];
			$SaveTheCows = TableMng::query("SELECT * FROM SystemGrades WHERE ID = $ID");
			// Cows stands for Code of worst systematic
			$gradesAll[] = $SaveTheCows[0];
		}
		$users = TableMng::query(
			'SELECT * FROM SystemUsers ORDER BY name ASC'
		);
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

		$jointsUsersInGrade = TableMng::query('SELECT * FROM SystemUsersInGradesAndSchoolyears');
		$grades = TableMng::query('SELECT * FROM SystemGrades');
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

		$payed = TableMng::query(
			'SELECT a.*, lc.abbreviation AS loanChoice
				FROM SchbasAccounting a
				LEFT JOIN SchbasLoanChoices lc ON lc.ID = a.loanChoiceId
		');
	//	$fees = TableMng::query('SELECT * FROM SchbasFee', true);
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
			TableMng::query("UPDATE SchbasAccounting SET payedAmount=$payment WHERE UID=$UID");
			//die("UPDATE SchbasAccounting SET payedAmount=$payment WHERE 'UID'=$UID");
		} catch (Exception $e) {
			//die("UPDATE SchbasAccounting SET 'payedAmount'=$payment WHERE 'UID'=$UID".$e);
		}

	}


	function userRemoveByID() {
		$uid = TableMng::getDb()->real_escape_string($_POST['UserID']);
		$uidArray = explode(' ', $uid);

		if(count($uidArray) == 2) {

			$uid = $uidArray[0];

			$query = sprintf("SELECT COUNT(*) FROM SchbasAccounting WHERE `UID`='%s'",$uid);
			$result=TableMng::query($query);
			if (!$result[0]['COUNT(*)']!="0") {
				$this->SchbasAccountingInterface->dieError('Benutzer hat noch keinen Antrag abgegeben!');
			}
			if(is_numeric($uid)) {
				try {
					$query = sprintf("DELETE FROM SchbasAccounting WHERE `UID`='%s'",$uid);

					TableMng::query($query);
					$this->SchbasAccountingInterface->showDeleteSuccess();
				} catch (Exception $e) {
					var_dump($e->getMessage());
				}
			}
			else {
				$this->SchbasAccountingInterface->dieError('Benutzer-ID nicht g&uuml;ltig');
			}
		}
		else {
			$this->SchbasAccountingInterface->dieError('Bitte auszutauschenden oder neuen Antrag einscannen!');
		}
	}

	private function remember(){	// function prints lend books

		$showIdAfterName = false;				// to enable the id after name set this value to true

		$lending = TableMng::query('SELECT * FROM SchbasLending');

		for ($i=0; $i < (count($lending)); $i++){	// one loop prodices one line of the table
			//name
			$id = (int) $lending[$i]["user_id"];
			$name = TableMng::query("SELECT name FROM SystemUsers WHERE ID=$id");
			$name = $name[0]["name"];
			$forename = TableMng::query("SELECT forename FROM SystemUsers WHERE ID=$id");
			$forename = $forename[0]["forename"];
			if($showIdAfterName == true){
				$schueler = ("$forename $name($id)");
			}else{
				$schueler = ("$forename $name");
			}
			$schueler_arr[] = $schueler;

			//class
			try{
				$schoolyearDesired = TableMng::query('SELECT ID FROM SystemSchoolyears WHERE active = 1');
				$schoolyearID = $schoolyearDesired[0]['ID'];
				$gradeID = TableMng::query(sprintf("SELECT GradeID FROM SystemUsersInGradesAndSchoolyears WHERE UserID = '$id' AND schoolyearID = $schoolyearID"));
				$gradeIDtemp = (int)$gradeID[0]['GradeID'];
				$gradelevel = TableMng::query(sprintf("SELECT gradelevel FROM SystemGrades WHERE ID = $gradeIDtemp"));
				$grade = $gradelevel[0]['gradelevel'];
				$label = TableMng::query(sprintf("SELECT label FROM SystemGrades WHERE ID = $gradeIDtemp"));
				$label = $label[0]['label'];
			}catch (Exception $e){
				$grade = 0;
			}
			$class = "$grade-$label";
			$class_arr[]= $class;

			//book
			$bookid = (int) $lending[$i]["inventory_id"];
			$title = TableMng::query("SELECT title FROM SchbasBooks WHERE id=$bookid");
			$book[] = $title[0]["title"];

			//date
			$date[] = $lending[$i]["lend_date"];
			//$date = date_format('%d.%m.%Y');
			//$date[] = $date;
			//$date[] = date_format(strtodate($lending[$i]["lend_date"]),"%d.%m.%Y");

		}
		$this->SchbasAccountingInterface->showRememberList($schueler_arr, $class_arr, $book, $date, count($lending)-1);
	}

	private function getStudentIDsOfClass($gradeId){
		$ids = TableMng::query("SELECT userId
			FROM SystemUsersInGradesAndSchoolyears uigs
			JOIN SystemSchoolyears s ON uigs.schoolyearId = s.ID
			WHERE gradeId='$gradeId' AND s.active = true
		");
		$nr = count($ids);
		$studentIDs;
		for($i=0;$i<$nr;$i++){
			$studentIDs[$i] = $ids[$i]["userId"];
		}
		return $studentIDs;
	}

	private function getNameOfStudentId($studentId){
		$name = TableMng::query("SELECT name FROM SystemUsers WHERE ID='$studentId'");
		$name = $name[0]["name"];
		return $name;
	}

	private function getForenameOfStudentId($studentId){
		$forename = TableMng::query("SELECT forename FROM SystemUsers WHERE ID='$studentId'");
		$forename = $forename[0]["forename"];
		return $forename;
	}

	private function getBooksOfStudentId($studentId){
		$books = TableMng::query("SELECT inventory_id FROM SchbasLending WHERE user_id='$studentId'");
		$booklist = "";
		$nr = count($books);
		for($i=0;$i<$nr;$i++){
			$bookid = TableMng::query("SELECT book_id FROM SchbasInventory WHERE id='".$books[$i]["inventory_id"]."'");
			$bookIDs[] = $bookid[0]["book_id"];
		}

		for ($i=0;$i<$nr;$i++){
			$bookName = TableMng::query("SELECT title FROM SchbasBooks WHERE id='$bookIDs[$i]'");
			if (!empty($bookName)) {
			$bookName = $bookName[0]["title"];
			if ($i==0){
				$booklist = "$bookName";
			}else{
				$booklist = "$booklist </br> $bookName";
			}
			}
		}

		return $booklist;
	}

	private function getBooksOfStudentIdRebmemer($studentId, $class){
		$books = $this->lm->getLoanByUID($studentId, false);
		$loanbooksSelfBuy = TableMng::query("SELECT BID FROM SchbasSelfpayer WHERE UID=".$studentId);
		$loanbooksSelfBuy = array_map('current',$loanbooksSelfBuy);
		$checkedBooks = array();
		foreach ($books as $book) {
			if (!in_array($book['id'],$loanbooksSelfBuy)) $checkedBooks[] = $book;

		}

		$booklist = "";
		foreach ($checkedBooks as $book) {
			$booklist .= "<li>".$book["title"];
		}
// 		$books = TableMng::query("SELECT inventory_id FROM SchbasLending WHERE user_id='".$studentId."'");
// 		$booklist = "";
// 		if (!empty($books)) {
// 		// format class to right format (in table int(7) is string(2)"07" )
// 		if ($class < 10)
// 			$class = "0".$class;


// 		for($i=0;$i<count($books); $i++){
// 			$bookIDs[] = $books[$i]["inventory_id"];
// 		}

// 		$should = TableMng::query("SELECT id FROM SchbasBooks WHERE class='".$class."'");

// 		for ($i=0; $i<count($should); $i++){
// 			$shouldIDs[] = $should[$i]["id"];
// 		}

// 		$bookIDs1 = array_diff($shouldIDs, $bookIDs);

// 		// FINDING THE NAMES

// 		for ($i=0;$i<count($bookIDs1);$i++){
// 			$bookName = TableMng::query("SELECT title FROM SchbasBooks WHERE id='$bookIDs1[$i]'");
// 			//var_dump($bookName);echo "<br>";
// 			$bookName = $bookName[0]["title"];
// 			if ($i==0){
// 				$booklist = $bookName;
// 			}else{
// 				$booklist .= "</br>".$bookName;
// 			}
// 		}
// 		}
		return $booklist;
	}


	private function remember2(){	// function prints lend books

		if(!isset($_GET['class'])){
			die("ERROR: No Class selected.");
		}else{

			$classId = $_GET['class'];

			$classNamelabel = TableMng::query("SELECT label FROM SystemGrades WHERE ID='$classId'");
			$classNamelabel = $classNamelabel[0]["label"];
			$classNamelevel = TableMng::query("SELECT gradelevel FROM SystemGrades WHERE ID='$classId'");
			$classNamelevel = $classNamelevel[0]["gradelevel"];
			$className = "$classNamelevel$classNamelabel";

			$studentIDs = $this->getStudentIDsOfClass($classId);

			$nrOfStudentIDs = count($studentIDs);
			for($i=0; $i<$nrOfStudentIDs; $i++){
				$name[] = $this->getNameOfStudentId($studentIDs[$i]);
				$forename[] = $this->getForenameOfStudentId($studentIDs[$i]);
				$books[] = $this->getBooksOfStudentID($studentIDs[$i]);
			}

			$listOfClasses = $this->getListOfClasses();

		}
		$this->SchbasAccountingInterface->showRememberList2($name, $forename, $books, $nrOfStudentIDs-1, $className, $listOfClasses);
	}

	private function getListOfClasses($func="remember2"){
		$gradesTbl = TableMng::query("SELECT * FROM SystemGrades");
		$nr = count($gradesTbl);

		$listOfClasses="";

		for ($i=0; $i<$nr; $i++){
			$gradesTblLine = $gradesTbl[$i];
			$gradeId = $gradesTblLine["ID"];
			$gradelabel = $gradesTblLine["label"];
			$gradelevel = $gradesTblLine["gradelevel"];
			$listOfClasses = "$listOfClasses <a class='btn btn-default btn-sm' href='./index.php?section=Schbas|SchbasAccounting&action=".$func."&class=$gradeId'>$gradelevel$gradelabel</a>";
		}
		return $listOfClasses;
	}

	private function rebmemer2(){		// REBMEMER IS REMEMBER BACKWARDS, BECAUSE IT DOES THE OPPOSITE (and i like it... ;P)
		if(!isset($_GET['class'])){
			die("ERROR: No Class selected.");
		}else{
			$classId = $_GET['class'];

			$classNamelabel = TableMng::query("SELECT label FROM SystemGrades WHERE ID='$classId'");
			$classNamelabel = $classNamelabel[0]["label"];
			$classNamelevel = TableMng::query("SELECT gradelevel FROM SystemGrades WHERE ID='$classId'");
			$classNamelevel = $classNamelevel[0]["gradelevel"];
			$className = "$classNamelevel$classNamelabel";

			$studentIDs = $this->getStudentIDsOfClass($classId);

			$nrOfStudentIDs = count($studentIDs);	// excluded from for loop to increase speed.... (dont like it? channge it...)
			for($i=0; $i<$nrOfStudentIDs; $i++){
				$name[] = $this->getNameOfStudentId($studentIDs[$i]);
				$forename[] = $this->getForenameOfStudentId($studentIDs[$i]);
				$books[] = $this->getBooksOfStudentIDRebmemer($studentIDs[$i],$classNamelevel);
			}

			$listOfClasses = $this->getListOfClasses("rebmemer2");
		}
		$this->SchbasAccountingInterface->showRebmemerList2($name, $forename, $books, $nrOfStudentIDs-1, $className, $listOfClasses);

	}

	private function deleteAll()
	{
		try {
			$stmt = $this->_pdo->query('TRUNCATE TABLE schbas_accounting');
			$stmt->execute();
			$this->SchbasAccountingInterface->dieSuccess('Tabelle Buchhaltung erfolgreich geleert!');
		} catch (PDOException $e) {
			$this->SchbasAccountingInterface->dieError('Konnte die Tabelle Buchhaltung nicht leeren!');
		}

	}
}
?>
