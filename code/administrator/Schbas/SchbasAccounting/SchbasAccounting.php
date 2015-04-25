<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Schbas/Schbas.php';
require_once PATH_INCLUDE . '/Schbas/Loan.php';

class SchbasAccounting extends Schbas {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////



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

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->_loanHelper = new \Babesk\Schbas\Loan($dataContainer);
	}

	/**
	 * send reminders via the message module to all users who haven't payed the
	 * complete fee yet. this function uses the selected reminder template for usage.
	 */
	protected function sendReminder () {
		try {
			$loanHelper = new \Babesk\Schbas\Loan();
			$prepSchoolyear = $loanHelper->schbasPreparationSchoolyearGet();
			$template= TableMng::query("SELECT mt.title, mt.text FROM MessageTemplate AS mt WHERE mt.ID=(SELECT value FROM SystemGlobalSettings WHERE name='schbasReminderMessageID')");
			$author = TableMng::query("SELECT value FROM SystemGlobalSettings WHERE name='schbasReminderAuthorID'");
			$group = TableMng::query("SELECT ID FROM MessageGroups WHERE name='schbas'");
			TableMng::query("INSERT INTO MessageMessages (`ID`, `title`, `text`, `validFrom`, `validTo`, `originUserId`, `GID`) VALUES (NULL, '".$template[0]['title']."', '".$template [0]['text']."', '".date("Y-m-d")."', '".date("Y-m-d",strtotime("+4 weeks"))."', '".$author[0]['value']."', '".$group[0]['ID']."');");
			$messageID = TableMng::$db->insert_id;
			TableMng::query("INSERT INTO MessageManagers (`ID`, `messageID`, `userId`) VALUES (NULL, '".$messageID."','".$author[0]['value']."')");
			$usersToRemind = TableMng::query(
				"SELECT * FROM SchbasAccounting
				WHERE payedAmount < amountToPay
					AND schoolyearId = $prepSchoolyear
			");
			foreach ($usersToRemind as $user) {
				TableMng::query(
					"INSERT INTO MessageReceivers
					(`ID`, `messageID`, `userID`, `read`, `return`)
					VALUES (
						NULL, '$messageID', '".$user['userId']."',
						'0', 'noReturn'
					);
				");
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

		$formDataStr = filter_input(INPUT_POST, 'barcode');
		if(!$formDataStr) {
			$this->SchbasAccountingInterface->dieError(
				'Bitte auszutauschenden oder neuen Antrag einscannen!'
			);
		}
		$formData = explode(' ', $formDataStr);
		if(count($formData) != 2) {
			$this->SchbasAccountingInterface->dieError(
				'Bitte auszutauschenden oder neuen Antrag einscannen!'
			);
		}

		$prepSchoolyear = $this->preparationSchoolyearGet();
		list($userId, $loanChoiceStr) = $formData;
		if($userId && $loanChoiceStr) {

			$loanChoices = array('nl','ln','lr','ls');
			$user = $this->_em->find('DM:SystemUsers', $userId);
			if(!$user) {
				$this->SchbasAccountingInterface->dieError(
					'Konnte den Benutzer nicht finden.'
				);
			}
			$accounting = $this->_em->getRepository('DM:SchbasAccounting')
				->findOneBy(
					['user' => $user, 'schoolyear' => $prepSchoolyear]
				);
			if ($accounting) {
				http_response_code(409);
				die('Der Antrag für diesen Benutzer wurde bereits ' .
					'eingescannt. Bitte löschen sie ihn manuell, um ihn neu ' .
					'hinzuzufügen.');
			}
			if(!$this->isUserInSchoolyearCheck($user, $prepSchoolyear)) {
				http_response_code(400);
				die('Der Benutzer ist im Vorbereitungsschuljahr in keiner ' .
					'Klasse');
			}
			if(in_array($loanChoiceStr, $loanChoices, true)) {

				require_once PATH_INCLUDE . '/Schbas/Loan.php';
				$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
				$loanChoice = $this->_em->getRepository('DM:SchbasLoanChoice')
					->findOneByAbbreviation($loanChoiceStr);
				try {
					list($feeNormal, $feeReduced) = $loanHelper
						->loanPriceOfAllBookAssignmentsForUserCalculate($user);

					if ($loanChoice->getAbbreviation() == "ln") {
						$amountToPay = $feeNormal;
					}
					else if ($loanChoice->getAbbreviation() == "lr") {
						$amountToPay = $feeReduced;
					}
					else {
						$amountToPay = 0.00;
					}

					$accounting = new \Babesk\ORM\SchbasAccounting();
					$accounting->setUser($user);
					$accounting->setSchoolyear($prepSchoolyear);
					$accounting->setLoanChoice($loanChoice);
					$accounting->setPayedAmount(0.00);
					$accounting->setAmountToPay($amountToPay);
					$this->_em->persist($accounting);
					$this->_em->flush();
					die('success');
				}
				catch(\Exception $e) {
					$this->_logger->logO('Error adding accounting-entry',
						['sev' => 'error', 'moreJson' => $e->getMessage()]);
					die('Ein Fehler ist beim Hinzufügen aufgetreten.');
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

	protected function isUserInSchoolyearCheck($user, $schoolyear) {
		//Check if the user is in the SchbasPreparationSchoolyear
		$count = $this->_em->createQuery(
			'SELECT COUNT(u) FROM DM:SystemUsers u
				INNER JOIN u.attendances uigs
				INNER JOIN uigs.schoolyear s WITH s = :schoolyear
				WHERE u = :user
		')->setParameter('user', $user)
			->setParameter('schoolyear', $schoolyear)
			->getSingleScalarResult();
		return $count > 0;
	}

	private function showUsers () {
		$schoolyearDesired = TableMng::query('SELECT ID FROM SystemSchoolyears WHERE active = 1');
		$schoolyearID = $schoolyearDesired[0]['ID'];
		$gradeID = TableMng::query("SELECT DISTINCT gradeID FROM SystemAttendances WHERE schoolyearID = $schoolyearID");
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

		$jointsUsersInGrade = TableMng::query('SELECT * FROM SystemAttendances');
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

		$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
		$schoolyear = $loanHelper->schbasPreparationSchoolyearGet();
		$query = $this->_em->createQuery(
			'SELECT a, lc FROM DM:SchbasAccounting a
				INNER JOIN a.loanChoice lc
				WHERE a.schoolyear = :schoolyear
		');
		$query->setParameter('schoolyear', $schoolyear);
		$payed = $query->getResult();
		foreach($users as &$user) {
			foreach($payed as $pay) {
				if($pay->getUser()->getId() == $user['ID']) {
					$user['payedAmount'] = $pay->getPayedAmount();
					$user['amountToPay'] = $pay->getAmountToPay();
					$user['loanChoice'] = $pay->getLoanChoice()
						->getAbbreviation();
				}
			}
		}
		return $users;
	}


	private function executePayment($UID, $payment) {
		$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
		$schoolyear = $loanHelper->schbasPreparationSchoolyearGet();
		$schoolyearId = $schoolyear->getId();
		$UID = str_replace("Payment", "", $UID);
		try {
			TableMng::query(
				"UPDATE SchbasAccounting
					SET payedAmount = $payment
					WHERE userId = $UID AND schoolyearId = $schoolyearId
			");
		} catch (Exception $e) {

		}

	}


	function userRemoveByID() {

		$formDataStr = filter_input(INPUT_POST, 'UserID');
		if(!$formDataStr) {
			$this->SchbasAccountingInterface->dieError(
				'Bitte auszutauschenden oder neuen Antrag einscannen!'
			);
		}
		$formData = explode(' ', $formDataStr);
		if(count($formData) != 2) {
			$this->SchbasAccountingInterface->dieError(
				'Bitte auszutauschenden oder neuen Antrag einscannen!'
			);
		}

		list($userId, $loanChoice) = $formData;
		if($userId && $loanChoice) {
			$user = $this->_em->find('DM:SystemUsers', $userId);
			$prepSchoolyear = $this->preparationSchoolyearGet();
			if($user) {
				$accounting = $this->_em->getRepository('DM:SchbasAccounting')
					->findOneBy(
						['user' => $user, 'schoolyear' => $prepSchoolyear]
					);
				if($accounting) {
					$this->_em->remove($accounting);
					$this->_em->flush();
					$this->SchbasAccountingInterface->showDeleteSuccess();
				}
				else {
					$this->SchbasAccountingInterface->dieError(
						'Benutzer hat noch keinen Antrag abgegeben!'
					);
				}
			}
			else {
				$this->SchbasAccountingInterface->dieError(
					'Benutzer-ID nicht gültig'
				);
			}
		}
		else {
			$this->SchbasAccountingInterface->dieError(
				'Bitte auszutauschenden oder neuen Antrag einscannen!'
			);
		}
	}

	protected function preparationSchoolyearGet() {

		$schoolyear = false;
		$entry = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('schbasPreparationSchoolyearId');
		if($entry) {
			$schoolyear = $this->_em->find(
				'DM:SystemSchoolyears', $entry->getValue()
			);
		}
		if(!$schoolyear) {
			$this->_logger->logO('Could not fetch PreparationSchoolyear',
				['sev' => 'error']);
			$this->SchbasAccountingInterface->dieError('Ein Fehler ist ' .
				'beim Abrufen des Vorbereitungs-Schuljahres aufgetreten.');
		}
		return $schoolyear;
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
				$gradeID = TableMng::query(sprintf("SELECT GradeID FROM SystemAttendances WHERE UserID = '$id' AND schoolyearID = $schoolyearID"));
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
			FROM SystemAttendances uigs
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


		return $books;
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
			$name = $forename = $books = [];
			foreach($studentIDs as $userId) {
				$user = $this->_em->getReference('DM:SystemUsers', $userId);
				$name[] = $this->getNameOfStudentId($userId);
				$forename[] = $this->getForenameOfStudentId($userId);
				$books[] = $this->_loanHelper->loanBooksOfUserGet($user);
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


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}
?>
