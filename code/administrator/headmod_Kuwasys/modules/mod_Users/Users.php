<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/functions.php';
require_once PATH_INCLUDE . '/CsvExporter.php';
require_once 'UsersInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysGradeManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInGrade.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInSchoolYear.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysSchoolYearManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersInClassStatusManager.php';
require_once PATH_ADMIN . '/headmod_Kuwasys/KuwasysFilterAndSort.php';
require_once 'UsersPasswordResetter.php';
require_once 'DisplayUsersWaiting.php';
require_once 'UsersCsvImport.php';
require_once 'UsersCreateParticipationConfirmationPdf.php';
require_once 'UsersEmailParticipationConfirmation.php';

/**
 * Main-Class for the Module Users
 * allows adding, changing, showing and deleting Users
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Users extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $_interface;
	private $_usersManager;
	private $_jointUsersInGradeManager;
	private $_gradeManager;
	private $_schoolYearManager;
	private $_jointUsersInSchoolYear;
	private $_classManager;
	private $_jointUsersInClass;
	private $_databaseAccessManager;
	private $_usersInClassStatusManager;

	/**
	 * @var KuwasysLanguageManager
	 */
	private $_languageManager;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute ($dataContainer) {
		$this->entryPoint($dataContainer);
		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'addUser':
					$this->addUser();
					break;
				case 'csvImport':
					$this->importUsersFromCsv();
					break;
				case 'showUsers':
					$this->showUsers();
					break;
				case 'showUsersGroupedByYearAndGrade':
					$this->showUsersGroupedByYearAndGrade();
					break;
				case 'deleteUser':
					$this->deleteUser();
					break;
				case 'changeUser':
					$this->changeUserData();
					break;
				case 'addUserToClass':
					$this->addUserToClass();
					break;
				case 'moveUserByClass':
					$this->moveUserByClass();
					break;
				case 'changeUserToClass':
					$this->changeUserToClass();
					break;
				case 'showUserDetails':
					$this->showUserDetails();
					break;
				case 'showWaitingUsers':
					$this->showWaitingUsers();
					break;
				case 'printParticipationConfirmation':
					$this->handleParticipationConfirmationPdf();
					break;
				case 'printParticipationConfirmationForAll':
					$this->handleParticipationConfirmationPdfForAll ();
					break;
				case 'sendEmailsParticipationConfirmation':
					$this->emailParticipationConfirmation ();
					break;
				case 'resetPasswords':
					$this->resetPasswordOfAllUsers ();
					break;
				default:
					$this->_interface->dieError($this->_languageManager->getText('actionValueWrong'));
			}
		}
		else {
			$this->showMainMenu();
		}

	}

	////////////////////////////////////////////////////////////////////////////
	//Implements
	private function entryPoint ($dataContainer) {

		defined('_AEXEC') or die('Access denied');
		$this->_usersManager = new KuwasysUsersManager();
		$this->_gradeManager = new KuwasysGradeManager();
		$this->_schoolYearManager = new KuwasysSchoolYearManager();
		$this->_jointUsersInGradeManager = new KuwasysJointUsersInGrade();
		$this->_jointUsersInSchoolYear = new KuwasysJointUsersInSchoolYear();
		$this->_classManager = new KuwasysClassManager();
		$this->_jointUsersInClass = new KuwasysJointUsersInClass();
		$this->_interface = new UsersInterface($this->relPath, $dataContainer->getSmarty());
		$this->_languageManager = $dataContainer->getLanguageManager();
		$this->_languageManager->setModule('Users');
		require_once PATH_ADMIN . $this->relPath . '../../KuwasysDatabaseAccess.php';
		$this->_databaseAccessManager = new KuwasysDatabaseAccess($this->_interface);
		$this->_usersInClassStatusManager = new KuwasysUsersInClassStatusManager ();
	}

	/**-------------------------------------------------------------------------
	 * Entry-point-functions for different parts of the module
	 *------------------------------------------------------------------------*/

	/**
	 * adds a User to the MySQL-table
	 */
	private function addUser () {


		if (isset($_POST['username'], $_POST['name'], $_POST['forename'], $_POST['telephone'])) {

			$this->checkAddUserInput(); //check User Input
			$this->checkPasswordRepetition(); //is repeated Password the same?

			$birthday = $this->handleBirthday($_POST['Date_Day'],
				$_POST['Date_Month'], $_POST['Date_Year']);

			$this->addUserToDatabase($_POST['forename'], $_POST['name'],
				$_POST['username'], md5($_POST['password']), $_POST['email'],
				$_POST['telephone'], $birthday, $_POST['schoolyear'],
				$_POST['grade']);

			$this->_interface->dieMsg(sprintf($this->_languageManager->getText('finishedAddUser'), $_POST['forename'], $_POST['name']));
		}
		else {
			$this->showAddUser();
		}
	}

	private function addUserToDatabase($forename, $name,$username,
		$hashedPassword, $email,$telephone, $birthday, $schoolyearId, $gradeId) {
		$db = TableMng::getDb();
		TableMng::sqlSave($forename);
		TableMng::sqlSave($name);
		TableMng::sqlSave($username);
		TableMng::sqlSave($hashedPassword);
		TableMng::sqlSave($email);
		TableMng::sqlSave($telephone);
		TableMng::sqlSave($birthday);
		TableMng::sqlSave($schoolyearId);
		TableMng::sqlSave($gradeId);

		$query = sprintf(
			"INSERT INTO users (forename, name, username, password,
				email, telephone, birthday) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s');
			SET @last_user_id = LAST_INSERT_ID();
			INSERT INTO jointUsersInSchoolYear (UserID, SchoolYearID)
				VALUES (@last_user_id, '%s');
			INSERT INTO jointUsersInGrade (UserID, GradeID)
				VALUES (@last_user_id, '%s');",
				$forename, $name,$username, $hashedPassword, $email,
				$telephone, $birthday, $schoolyearId, $gradeId);

		$db->autocommit(false);//mySQL-transaction
		if($db->multi_query($query)) {
			do {
				$db->next_result();
			} while($db->more_results());
		}
		if(!$db->errno) {
			$db->commit();
		}
		else {
			$this->_interface->dieError('Ein Fehler ist beim Verbinden mit der Datenbank aufgetreten' . $db->error);
		}
	}

	// private function addUserToDatabase ($forename, $name, $username, $password, $email, $telephone, $birthday) {

	// 	$hashedPassword = hash_password($password);
	// 	$query = "INSERT INTO users (forename, name, username, password,
	// 		email, telephone, birthday) VALUES (?, ?, ?, ?, ?, ?, ?)";
	// 	if($stmt = TableMng::getDb()->prepare($query)) {
	// 		$stmt->bind_param("sssssss", $forename, $name,
	// 			$username, $hashedPassword,
	// 			$email, $telephone, $birthday);
	// 		if($stmt->execute()) {
	// 			//everything was good
	// 		}
	// 		else {
	// 			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorAddUser'), TableMng::getDb()->error));
	// 		}
	// 	}
	// 	else {
	// 		$this->_interface->dieError('Ein Fehler ist beim Verbinden mit der Datenbank aufgetreten.' . TableMng::getDb()->error);
	// 	}
	// }

	private function deleteUser () {

		if (isset($_POST['dialogConfirmed'])) {
			$this->deleteUserFromDatabase();
			$this->deleteAllJointsUsersInGradeOfUser($_GET['ID']);
			$this->deleteJointUsersInSchoolYearByUserId($_GET['ID']);
			$this->_interface->dieMsg($this->_languageManager->getText('finDeleteUser'));
		}
		else if (isset($_POST['dialogNotConfirmed'])) {
			$this->_interface->dieMsg($this->_languageManager->getText('deleteUserNotComfirmed'));
		}
		else {
			$this->showDeleteUserConfirmation();
		}
	}

	private function changeUserData () {

		if (isset($_POST['forename'], $_POST['name'], $_POST['email'])) {

			$this->checkInputChangeUserData();
			$this->ChangeUserDataToDatabase();
			$this->changeJointUsersInGradeByUser();
			$this->changeJointUsersInSchoolYearByChangeUser();
			$this->_interface->dieMsg($this->_languageManager->getText('finChangeUser'));
		}
		else {
			$this->showChangeUserData();
		}
	}

	private function addUserToClass () {

		if (isset($_POST['classId'], $_GET['ID'])) {
			$statusId = $this->statusIdOfStatusNameGet ($_POST['classStatus']);
			if(!$this->hasClassWithSameDayAndStatusIdAs($_POST['classId'], $_GET['ID'], $statusId)) {
				$this->addJointUsersInClass($_GET['ID'], $_POST['classId'], $_POST['classStatus']);
				$this->_interface->dieMsg($this->_languageManager->getText('finAddUserToClass'));
			}
			else {
				$this->_interface->dieError ('Der Schüler hat an diesem Tag bereits einen Kurs mit diesem Status. Bitte passen sie zuerst diesen Kurs an');
			}
		}
		else {
			$this->showAddClassToUser();
		}
	}

	private function statusIdOfStatusNameGet ($statusName) {
		$query = 'SELECT ID FROM usersInClassStatus WHERE name = "' . $statusName . '"';
		try {
			$id = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError('Konnte einen Status nicht finden');
		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Status-ID nicht abrufen');
		}
		return $id [0] ['ID'];
	}

	private function changeUserToClass () {

		if (isset($_POST['classStatus'])) {
			$this->alterJointUsersInClassOfUserIdAndClassId($_GET['userId'], $_GET['classId'], $_POST['classStatus']);
		}
		else {
			$this->showChangeClassToUser();
		}
	}

	private function showWaitingUsers () {

		$displayUsersWaitingObj = new DisplayUsersWaiting($this->_interface, $this->_languageManager);
		$displayUsersWaitingObj->execute();
	}

	private function importUsersFromCsv () {

		if (count($_FILES)) {
			UsersCsvImport::classInit ($this->_interface, $this->_databaseAccessManager);
			UsersCsvImport::import ($_FILES['csvFile']['tmp_name'], ';');
		}
		else {
			$this->_interface->showSelectCsvFileForImport();
		}
	}

	private function moveUserByClass () {

		if(isset($_GET['classIdOld'], $_GET['userId'], $_POST['classIdNew'], $_POST['statusNew'])) {

			if ($this->moveUserByClassCheck ()) {
				$this->moveUserByClassCleanSessionVars();
				$this->moveUserByClassToDatabase($_GET['userId'], $_GET['classIdOld'], $_POST['classIdNew'], $_POST['statusNew']);
				$this->_interface->dieMsg($this->_languageManager->getText('finishedMoveUserToClass'));
			}
		}
		else if (isset($_GET['classIdOld'], $_GET['userId'])) {
			$this->showMoveUserByClass();
		}
		else {
			$this->_interface->dieError($this->_languageManager->getText('getIdWrong'));
		}
	}

	private function moveUserByClassCleanSessionVars () {
		if(isset($_SESSION ['moveUserByClassMaxRegConf'])) {
			unset($_SESSION ['moveUserByClassMaxRegConf']);
		}
		if(isset($_SESSION ['moveUserByClassOnDupDelete'])) {
			unset($_SESSION ['moveUserByClassOnDupDelete']);
		}
	}

	/**
	 * Checks if the circumstances for moving the user are correct; else show
	 * warnings and dialogs
	 */
	private function moveUserByClassCheck () {
		$shouldAdd = true;
		//check if the added Link would make the class bigger than maxRegistration allows
		if($this->isClassMaxRegistrationReached($_POST['classIdNew'])
			&& !isset($_SESSION ['moveUserByClassMaxRegConf'])
			&& !isset ($_POST ['ignoreMaxReg'])) {
			$shouldAdd = $this->moveUserByClassDialog ();
			if(!$shouldAdd) {
				return false;
			}
			else {
				$_SESSION ['moveUserByClassMaxRegConf'] = true;
			}
		}
		//check if there is already a Link with same status and unit, which would cause the programm to error out on specific Modules
		if($this->hasClassWithSameDayAndStatusIdAs ($_POST['classIdNew'],
			$_GET['userId'], $_POST['statusNew'])) {
			$shouldAdd = $this->moveUserByClassDuplicateDialog();
			if(!$shouldAdd) {
				return false;
			}
		}
		//no problem, add the new link
		else {
			return true;
		}
		return $shouldAdd;
	}

	private function moveUserByClassDuplicateDialog() {
		if (isset ($_POST['removeOldUicLink'])) {
			$this->moveUserByClassDuplicateDeleteOldLink();
			return true;
		}
		else {
			$this->moveUserByClassDuplicateDialogShow ($_GET['classIdOld'], $_POST['classIdNew'], $_GET['userId'], $_POST['statusNew']);
			return false;
		}
	}

	private function moveUserByClassDuplicateDeleteOldLink() {
		$whereQuery = $this->moveUserByClassDuplicateDialogDeleteOldLinkGetWhereQuery();
		$query = sprintf(
			'DELETE FROM jointUsersInClass WHERE %s
			', $whereQuery);
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte die alten Links nicht löschen' . $e->getMessage());
		}
		$this->_interface->showMsg ('Die alten Links wurden erfolgreich gelöscht');
	}

	private function moveUserByClassDuplicateDialogDeleteOldLinkGetWhereQuery() {
		$whereQuery = '';
		foreach ($_SESSION ['moveUserByClassOnDupDelete'] as $link) {
			$whereQuery .= sprintf ('ID = "%s" OR ', $link);
		}
		$whereQuery= rtrim ($whereQuery, 'OR ');
		return $whereQuery;
	}

	private function moveUserByClassDuplicateDialogShow($classIdOld, $classIdNew, $userId, $statusId) {
		$dupClass = $this->moveUserByClassDuplicateDialogShowFetchData(
			$classIdOld, $classIdNew, $userId, $statusId);
		if (count($dupClass) > 1) {
			$this->_interface->showError ('Es sind bereits mehrere sehr ähnliche Links zu dem Benutzer vorhanden; Wenn fortgefahren wird, werden sie alle gelöscht');
		}
		$dupLinkArray = array ();
		foreach ($dupClass as $d) {
			$dupLinkArray [] = $d ['linkId'];
		}
		$_SESSION ['moveUserByClassOnDupDelete'] = $dupLinkArray;
		$this->_interface->showMoveUserByClassDuplicateDialog ($dupClass, $classIdOld, $classIdNew, $userId, $statusId);
	}

	private function moveUserByClassDuplicateDialogShowFetchData (
		$classIdOld, $classIdNew, $userId, $statusId) {
		//fetch some data to show the problem to the user
		$query = sprintf(
			"SELECT c.label AS class, uic.ID as linkId,
				(SELECT translatedName FROM kuwasysClassUnit WHERE ID = c.unitId)
				AS unit,
				(SELECT translatedName FROM usersInClassStatus WHERE ID = uic.statusId)
				AS status
			FROM class c
			INNER JOIN jointUsersInClass uic ON uic.ClassID = c.ID
			WHERE uic.UserID = %s
				AND uic.statusId = %s
				AND c.unitId = (SELECT unitId FROM class WHERE ID = %s)
			", $userId, $statusId, $classIdNew);
		try {
			$dupClass = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			throw $e;
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte die Kursdaten nicht abrufen.');
		}
		return $dupClass;
	}

	private function hasClassWithSameDayAndStatusIdAs ($classId, $userId, $statusId) {
		$query = sprintf(
			"SELECT COUNT(*) AS count
			FROM jointUsersInClass uic
			INNER JOIN class c ON uic.ClassID = c.ID
			WHERE uic.UserID = %s
				AND uic.statusId = %s
				AND c.unitId = (SELECT unitId FROM class WHERE ID = %s)
			", $userId, $statusId, $classId);
		try {
			$hasClass = TableMng::query ($query, true);
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte nicht auf weitere Kurse mit gleichem Status und Tag überprüfen' . $e->getMessage());
		}
		if ($hasClass[0] ['count'] != 0) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Checks if a confirmation-Dialog has to be shown to move the User
	 * Based on the maximum Registration of the Class
	 */
	private function moveUserByClassDialog () {
		if(isset($_POST['confirmed'])) { //Already confirmed the Dialog
			return true;
		}
		else if (isset($_POST['notConfirmed'])) { //Dialog declined
			$this->_interface->dieMsg($this->_languageManager->getText('moveUserToClassNotConfirmed'));
		}
		else { //show the Dialog
			$this->showMoveUserByClassClassFullConfirmation($_GET['userId'], $_GET['classIdOld'], $_POST['classIdNew'], $_POST['statusNew']);
		}
	}

	/**
	 * shows a UserList thats grouped by Schoolyears and Grades
	 */
	private function showUsersGroupedByYearAndGrade () {
		$schoolyearAll = $this->_databaseAccessManager->schoolyearGetAll();
		$schoolyearDesired = $this->getDesiredSchoolyear($schoolyearAll);
		$gradesOfDesiredSchoolyear = $this->getGradesOfSchoolyearDesired($schoolyearDesired);
		$gradeDesired = $this->getDesiredGrade($gradesOfDesiredSchoolyear);
		$users = $this->getAllUsersOfDesiredGrade($gradeDesired);
		try {
			$preUsers = $users;
			$users = KuwasysFilterAndSort::elementsFilter ($users);
			$users = KuwasysFilterAndSort::elementsSort ($users);
		} catch (Exception $e) {
			$users = $preUsers;
			$this->_interface->showMsg ('Konnte die Benutzer nicht nach den angegebenen Kriterien filtern. Hinweis: da hier einige
				Filteroptionen überflüssig sind, funktionieren sie auch nicht.');
		}
		$this->_interface->showUsersGroupedByYearAndGrade($schoolyearAll, $schoolyearDesired, $gradesOfDesiredSchoolyear,
				$gradeDesired, $users);
	}

	/**
	 * returns the Schoolyear the Admin wants, so that the program can display all Users of this Schoolyear
	 * @used-by Users::showUsersGroupedByYearAndGrade
	 * @return schoolyear[] the desired schoolyear
	 */
	private function getDesiredSchoolyear ($schoolyearAll) {

		if(isset($_GET['schoolyearIdDesired'])) {
			foreach ($schoolyearAll as $schoolyear) {
				//pick the schoolyear the Admin wants
				if($_GET['schoolyearIdDesired'] == $schoolyear ['ID']) {
					return $schoolyear;
				}
			}
		}
		else {
			foreach ($schoolyearAll as $schoolyear) {
				//pick the schoolyear thats active at the moment
				if($schoolyear ['active']) {
					return $schoolyear;
				}
			}
		}
		$this->_interface->dieError($this->_languageManager->getText('errorSelectDesiredSchoolyear'));
	}

	/**
	 * returns all Grades of the schoolyear $schoolyearDesired
	 * @param schoolyear[] $schoolyearDesired
	 * @return grades[] the grades of the schoolyear
	 */
	private function getGradesOfSchoolyearDesired ($schoolyearDesired) {

		$jointsGradeInSchoolyear = $this->_databaseAccessManager->jointGradeInSchoolyearGetBySchoolyearId($schoolyearDesired ['ID']);
		foreach ($jointsGradeInSchoolyear as $joint) {
			$this->_databaseAccessManager->gradeIdAddToFetchArray($joint ['GradeID']);
		}
		$grades = $this->_databaseAccessManager->gradeGetAllByFetchArray();
		return $grades;
	}

	/**
	 * returns the grade that the user has selected in the form. If no Grade has been selected by the User yet,
	 * it returns the first grade of $gradeDesired
	 * @param grades[grade[]] $gradeDesired
	 */
	private function getDesiredGrade ($gradeDesired) {

		if(isset($_GET['gradeIdDesired'])) {
			foreach ($gradeDesired as $grade) {
				if($grade ['ID'] == $_GET['gradeIdDesired']) {
					return $grade;
				}
			}
		}
		else {
			return $gradeDesired [0];
		}
		$this->_interface->dieError($this->_languageManager->getText('errorSelectDesiredGrade'));
	}

	/**
	 * returns all users of the desired grade
	 * @param unknown $gradeDesired
	 */
	private function getAllUsersOfDesiredGrade ($gradeDesired) {

		$jointsUserInGrade = $this->_databaseAccessManager->jointUserInGradeGetAllByGradeId($gradeDesired ['ID']);
		foreach ($jointsUserInGrade as $joint) {
			$this->_databaseAccessManager->userIdAddToUserIdArray($joint ['UserID']);
		}
		$users = $this->_databaseAccessManager->userGetByUserIdArray();
		return $users;
	}

	private function handleParticipationConfirmationPdf () {
		UsersCreateParticipationConfirmationPdf::init ($this->_interface);
		UsersCreateParticipationConfirmationPdf::execute ($_POST ['userIds']);
	}

	private function handleParticipationConfirmationPdfForAll () {
		$query = 'SELECT u.ID as userId
			FROM users u
				JOIN jointUsersInClass uic ON u.ID = uic.UserID
				JOIN usersInClassStatus uics ON uic.statusId = uics.ID
			WHERE uics.name = "active" OR uics.name = "waiting";';
		try {
			$data = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError ('Es wurden keine Schüler gefunden, für die man die Dokumente hätte drucken können');
		} catch (Exception $e) {
			$this->_interface->dieError ('konnte die Daten der Schüler nicht abrufen' . $e->getMessage ());
		}
		$userIds = array ();
		foreach ($data as $row) {
			$userIds [] = $row ['userId'];
		}
		UsersCreateParticipationConfirmationPdf::init ($this->_interface);
		UsersCreateParticipationConfirmationPdf::execute ($userIds);
	}

	private function resetPasswordOfAllUsers () {
		$usersPasswordResetter = new UsersPasswordResetter ($this->_interface,
			$this->_databaseAccessManager, $this->_languageManager);
		$usersPasswordResetter->execute ();
	}

	/**-----------------------------------------------------------------------------
	 * Functions for displaying forms and other stuff
	 *----------------------------------------------------------------------------*/

	private function showChangeUserData () {

		$userData = $this->getUserData($_GET['ID']);
		$userData = $this->setUpUserValueSelectedGrade($userData);
		$userData = $this->setUpUserValueSelectedSchoolyear($userData);
		$grades = $this->getAllGrades();
		$schoolyears = $this->getAllSchoolYears();
		$this->_interface->showChangeUser($userData, $grades, $schoolyears);
	}

	private function showDeleteUserConfirmation () {

		$userData = $this->getUserData($_GET['ID']);
		$userForename = $userData['forename'];
		$userName = $userData['name'];
		$this->_interface->showDeleteUserConfirmation($_GET['ID'], $userForename, $userName, $this->_languageManager);
	}

	private function showUsers () {

		$users = $this->getAllUsers();
		$users = $this->addGradeLabelToUsers($users);
		$users = $this->addSchoolyearLabelToUsers ($users);
		$users = KuwasysFilterAndSort::elementsSort ($users);
		$users = KuwasysFilterAndSort::elementsFilter ($users);
		$this->_interface->showAllUsers($users);
	}

	private function showMainMenu () {

		$this->_interface->showMainMenu();
	}

	private function showAddUser () {

		$grades = $this->getAllGrades();
		$grades = $this->addSchoolyearToGrades ($grades);
		$schoolYears = $this->getAllSchoolYears();
		$this->_interface->showAddUser($grades, $schoolYears);
	}

	private function addSchoolyearToGrades ($grades) {
		$joints = $this->_databaseAccessManager->jointGradeInSchoolyearGetAll ();
		foreach ($grades as &$grade) {
			foreach ($joints as $joint) {
				if ($grade ['ID'] == $joint ['GradeID']) {
					$grade ['schoolyearId'] = $joint ['SchoolYearID'];
					continue 2;
				}
			}
		}
		return $grades;
	}

	private function showAddClassToUser () {

		$classes = $this->getAllClasses();
		$user = $this->getUserData($_GET['ID']);
		$this->_interface->showAddUserToClassDialog($user, $classes);
	}

	private function showChangeClassToUser () {

		$class = $this->getClassByClassId($_GET['classId']);
		$user = $this->getUserData($_GET['userId']);
		$linkStatus = $_GET['classStatus'];
		$this->_interface->showChangeUserToClassDialog($user, $class, $linkStatus);
	}

	private function showUserDetails () {

		$userId = $_GET['ID'];
		$user = $this->getUserData ($userId);
		$user = $this->addClassesToUser ($user);
		$user = $this->addGradeLabelToSingleUser ($user);
		$this->_interface->showUserDetails($user);
	}

	private function showMoveUserByClass () {

		$classes = $this->_databaseAccessManager->classGetAll();
		$classOld = $this->searchClassArrayForClassWithId($_GET['classIdOld'], $classes);
		$user = $this->_databaseAccessManager->userGet($_GET['userId']);
		$statusArray = $this->_usersInClassStatusManager->statusGetAll ();
		$this->_interface->showMoveUserByClass($classOld, $user, $classes, $statusArray);
	}

	private function showMoveUserByClassClassFullConfirmation ($userId, $classIdOld, $classIdNew, $statusNew) {

		$user = $this->_databaseAccessManager->userGet($userId);
		$classOld = $this->_databaseAccessManager->classGet($classIdOld);
		$classNew = $this->_databaseAccessManager->classGet($classIdNew);
		$this->_interface->showMoveUserByClassClassFullConfirmation($user, $classOld, $classNew, $statusNew);
	}

	private function searchClassArrayForClassWithId ($classId, $classes) {

		foreach ($classes as $class) {
			if($class ['ID'] == $classId) {
				return $class;
			}
		}
		return NULL;
	}

	/**-----------------------------------------------------------------------------
	 * Functions for accessing Database-tables and dieing when error occuring
	*----------------------------------------------------------------------------*/

	/********************
	 * UserManager
	********************/



	private function ChangeUserDataToDatabase () {

		if ($_POST['password'] != '' && $_POST['password'] != NULL) {
			$this->_usersManager->changeUserWithPassword($_GET['ID'], $_POST['forename'], $_POST['name'], $_POST[
					'username'], $_POST['email'], $_POST['telephone'], hash_password($_POST['password']), isset($_POST ['isPresetPw']));
		}
		else {
			$this->_usersManager->changeUserWithoutPassword($_GET['ID'], $_POST['forename'], $_POST['name'], $_POST[
					'username'], $_POST['email'], $_POST['telephone'], isset($_POST ['isPresetPw']));
		}
	}

	/**
	 * @used-by Users::deleteUser
	 */
	private function deleteUserFromDatabase () {

		try {
			$this->_usersManager->deleteUser($_GET['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorDeleteUser'), $e->getMessage()));
		}
	}

	private function getAllUsers () {

		try {
			$users = $this->_usersManager->getTableData();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoUsers'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchAllUsers'));
		}
		return $users;
	}

	/**
	 * @used-by Users::showDeleteUserConfirmation
	 * @param unknown_type $ID
	 */
	private function getUserData ($ID) {

		try {
			$userData = $this->_usersManager->getUserByID($ID);
		} catch (MySQLConnectionException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorConnectDatabase'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorGetUser'));
		}
		return $userData;
	}

	/********************
	 * KuwasysUsersInGradeManager
	********************/

	private function addJointUsersInGrade ($userID, $gradeID) {

		$query = "INSERT INTO jointUsersInGrade (UserID, GradeID)
			VALUES (?, ?)";
		if($stmt = TableMng::getDb()->prepare($query)) {
			$stmt->bind_param('ii', $userID, $gradeID);
			if(!$stmt->execute()) {
				$this->_interface->dieError($this->_languageManager->getText('errorAddJointUsersInGrade'));
			}
		}
		else {
			$this->_interface->dieError('Konnte nicht zur Datenbank Verbinden');
		}
	}

	/**
	 * Returns all Links between the Users and the Grades
	 */
	private function getAllJointsUsersInGrade () {

		try {
			$joints = $this->_jointUsersInGradeManager->getAllJoints();
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

	/**
	 * deletes a link between an User and a Grade that has the ID $jointID
	 */
	private function deleteJointUsersInGrade ($jointID) {

		try {
			$this->_jointUsersInGradeManager->deleteJoint($jointID);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoJointUsersInGradeToDelete'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteJointUsersInGrade'));
		}
	}

	/**
	 * deletes all links between the users-Table and the Grade-table that possess the UserID $userID
	 * @param numeric_string $userID The ID of the User
	 */
	private function deleteAllJointsUsersInGradeOfUser ($userID) {

		try {
			$this->_jointUsersInGradeManager->deleteJointsByUserId($userID);
		} catch (Exception $e) {
			$this->_interface->ShowMsg($this->_languageManager->getText('errorDeleteJointUsersInGrade'));
		}
	}

	private function getJointUsersInGradeByUserId ($userID) {

		try {
			$jointUsersInGrade = $this->_jointUsersInGradeManager->getJointByUserId($userID);
		} catch (Exception $e) {
			$this->_interface->showMsg($this->_languageManager->getText('errorFetchJointUsersInGrade'));
		}
		if (isset($jointUsersInGrade)) {
			return $jointUsersInGrade;
		}
	}

	/**
	 * this function changes the links between the Users and grades accordingly to the changes the User made in the form
	 */
	private function changeJointUsersInGradeByUser () {

		$jointUsersInGrade = $this->getJointUsersInGradeByUserId($_GET['ID']);

		if (!isset($jointUsersInGrade)) {
			if ($_POST['grade'] == 'NoGrade') {
				return;
			}
			else {
				$this->addJointUsersInGrade($_GET['ID'], $_POST['grade']);
			}
		}
		else {
			if ($_POST['grade'] == 'NoGrade') {
				$this->deleteJointUsersInGrade($jointUsersInGrade['ID']);
			}
			else if ($jointUsersInGrade['UserID'] != $_GET['ID'] || $jointUsersInGrade['GradeID'] != $_POST['grade']) {
				$this->deleteJointUsersInGrade($jointUsersInGrade['ID']);
				$this->addJointUsersInGrade($_GET['ID'], $_POST['grade']);
			}
			else {
				return;
			}
		}
	}

	/********************
	 * KuwasysGradeManager
	********************/

	/**
	 * returns all Grades
	 */
	private function getAllGrades () {

		try {
			$grades = $this->_gradeManager->getAllGrades();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoGrades'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchGrades'));
		}
		return $grades;
	}

	/**
	 * adds a Grade-Label to the User-Array to allow displaying the grade of the User
	 */
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

	private function addGradeLabelToSingleUser ($user) {

		$jointUsersInGrade = $this->getJointUsersInGradeByUserId($user['ID']);
		$grade = $this->getGradeByGradeIdWithoutDieingAtError($jointUsersInGrade['GradeID']);
		$user['gradeLabel'] = $grade['label'];
		$user['gradeValue'] = $grade['gradeValue'];
		$user['gradeId'] = $grade['ID'];
		return $user;
	}

	private function getGradeByGradeId ($gradeID) {

		try {
			$grade = $this->_gradeManager->getGrade($gradeID);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchGrade'));
		}
		return $grade;
	}

	private function getGradeByGradeIdWithoutDieingAtError ($gradeID) {

		try {
			$grade = $this->_gradeManager->getGrade($gradeID);
		} catch (Exception $e) {
			$this->_interface->showMsg($this->_languageManager->getText('errorFetchGrade'));
		}
		if (isset($grade)) {
			return $grade;
		}
	}

	/**
	 * adds the 'gradeIDSelected'-key to the user-array, to allow displaying the selected grade
	 * in the change-User-form
	 */
	private function setUpUserValueSelectedGrade ($user) {

		$jointUsersInGrade = $this->getJointUsersInGradeByUserId($user['ID']);
		if (!isset($jointUsersInGrade)) {
			return $user;
		}
		$grade = $this->getGradeByGradeId($jointUsersInGrade['GradeID']);
		$user['gradeIDSelected'] = $grade['ID'];
		return $user;
	}

	/********************
	 * KuwasysSchoolYearManager
	********************/

	private function getAllSchoolYears () {

		try {
			$schoolYears = $this->_schoolYearManager->getAllSchoolYears();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYears'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYears'));
		}
		return $schoolYears;
	}

	private function getSchoolyearLabelOfSchoolyear ($schoolyearID) {

		try {
			$schoolyearLabel = $this->_schoolYearManager->getSchoolyearLabel ($schoolyearID);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYearLabel'));
		}
		catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYear'));
		}
		return $schoolyearLabel;
	}

	private function getSchoolyearBySchoolyearId ($schoolyearID) {

		try {
			$schoolyear = $this->_schoolYearManager->getSchoolYear($schoolyearID);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYear'));
		}
		catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYear'));
		}
		return $schoolyear;
	}


	/********************
	 * KuwasysJointUsersInSchoolYearManager
	********************/

	/**
	 * this function adds a Link between an User and a Schoolyear to the Database using the
	 * KuwasysJointUsersInSchoolYearManager class
	 * @param string $userId
	 * @param string $schoolyearId
	 */
	private function addJointUsersInSchoolYear ($userId, $schoolyearId) {

		try {
			$this->_jointUsersInSchoolYear->addJoint($userId, $schoolyearId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddLinkUsersInSchoolyear'));
		}
	}

	private function deleteJointUsersInSchoolYearByUserId ($userId) {

		try {
			$this->_jointUsersInSchoolYear->deleteJointByUserId($userId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteJointUsersInSchoolYear'));
		}
	}

	private function getAllJointsUsersInSchoolyear () {

		try {
			$joints = $this->_jointUsersInSchoolYear->getAllJoints();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoSchoolYears'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchSchoolYears'));
		}
		return $joints;
	}

	/**
	 * searches the Table of links between user and schoolyear, and returns the the schoolyearID of the Element
	 * that has the User-ID $userID
	 * @param unknown_type $userID
	 * @return unknown
	 */
	private function getSchoolyearIdOfUserId ($userID) {

		try {
			$schoolyearID = $this->_jointUsersInSchoolYear->getSchoolYearIdByUserId($userID);
		} catch (MySQLVoidDataException $e) {
			return;
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointUsersInSchoolYear'));
		}
		return $schoolyearID;
	}

	private function setUpUserValueSelectedSchoolyear ($user) {

		$schoolyearId = $this->getSchoolyearIdOfUserId($user['ID']);
		if (!isset($schoolyearId)) {
			return $user;
		}
		$user['schoolyearIdSelected'] = $schoolyearId;
		return $user;
	}

	/**
	 * This function adds a Link between Users and SchoolYear using the Parameters the addUser-Form returned
	 */
	private function addJointUsersInSchoolYearByAddUser () {

		$query = "INSERT INTO jointUsersInSchoolYear (UserID, SchoolYearID)
			VALUES (?,?)";
		if($stmt = TableMng::getDb()->prepare($query)) {
			$stmt->bind_param("ii", $_POST['userId'], $_POST['schoolyear']);
			if(!$stmt->execute()) {
				$this->_interface->dieError($this->_languageManager->getText('errorAddLinkUsersInSchoolyear') . TableMng::getDb()->error);
			}
		}
		else {
			$this->_interface->dieError('Ein Fehler ist beim Verbinden zur Datenbank aufgetreten.' . TableMng::getDb()->error);
		}
	}

	/**
	 * changes entries of the Table jointUsersInSchoolyear according to the values the User has given in the form changeUser
	 */
	private function changeJointUsersInSchoolYearByChangeUser () {

		$schoolyearBeforeId = $this->getSchoolyearIdOfUserId($_GET['ID']);

		if (!isset($_POST['schoolyear']) || $_POST['schoolyear'] == 0) {
			$this->_interface->dieError($this->_languageManager->getText('errorInputJointUsersInSchoolYear'));
		}

		if ($_POST['schoolyear'] != $schoolyearBeforeId) {
			try {
				$this->deleteJointUsersInSchoolYearByUserId($_GET['ID']);
			} catch (Exception $e) {
				$this->_interface->showError($this->_languageManager->getText('errorDeleteJointUsersInSchoolYear'));
			}
			try {
				$this->addJointUsersInSchoolYear($_GET['ID'], $_POST['schoolyear']);
			} catch (Exception $e) {
				$this->_interface->dieError($this->_languageManager->getText('errorAddLinkUsersInSchoolyear'));
			}
		}
	}

	/**
	 * adds a Schoolyear-Label to the User-Array to allow displaying the Schoolyear of the User in ShowUsers
	 */
	private function addSchoolyearLabelToUsers ($users) {

		$jointsUsersInSchoolyear = $this->_databaseAccessManager->dbAccessExec (
			KuwasysDatabaseAccess::JUserInSchoolyearManager, 'getAllJoints');
		$schoolyears = $this->getAllSchoolYears ();
		foreach ($users as & $user) {
			$user['schoolyearLabel'] = '';
			foreach ($jointsUsersInSchoolyear as $joint) {
				if ($joint['UserID'] == $user['ID']) {
					foreach ($schoolyears as $schoolyear) {
						if ($schoolyear['ID'] == $joint['SchoolYearID']) {
							$user['schoolyearLabel'] = $schoolyear ['label'];
						}
					}
				}
			}
		}
		return $users;
	}

	/********************
	 * KuwasysClassManager
	********************/

	private function getAllClasses () {

		try {
			$classes = $this->_classManager->getAllClasses();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClasses'));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClasses'));
		}
		return $classes;
	}

	private function getClassByClassId ($classId) {

		try {
			$class = $this->_classManager->getClass($classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClass'));
		}
		return $class;
	}

	/**
	 * Returns the Classes of the specific User
	 */
	private function getClassesOfUser ($userId) {

	}

	private function addClassesToUser ($user) {

		$classes = $this->_databaseAccessManager->classSWeekdayGetByUser ($user ['ID']);
		$user ['classes'] = $classes;
		return $user;
		// $userSpecificClasses = array();
		// $userSpecificJointsUserInClass = $this->getAllJointsOfUserId($user['ID']);
		// if (isset($userSpecificJointsUserInClass) && $userSpecificJointsUserInClass) {
		// 	foreach ($userSpecificJointsUserInClass as $joint) {
		// 		$status = $this->_databaseAccessManager->usersInClassStatusGetWithoutDieing ($joint ['statusId']);
		// 		$class = $this->getClassByClassId($joint['ClassID']);
		// 		if ($status) {
		// 			$class ['status'] = $status ['translatedName'];
		// 		}
		// 		$user['classes'][] = $class;
		// 	}
		// }
		// return $user;
	}

	/********************
	 * KuwasysJointUsersInClassManager
	********************/

	/**
	 *
	 * @param unknown_type $userID
	 * @param unknown_type $classID
	 */
	private function addJointUsersInClass ($userID, $classID, $statusName) {

		$query = sprintf ('INSERT INTO jointUsersInClass
			(UserID, ClassID, statusId) VALUES (%s, %s,
				(SELECT ID FROM usersInClassStatus WHERE name="%s")
				)', $userID, $classID, $statusName);
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddJointUsersInClass') . $e->getMessage ());
		}
	}

	private function getAllJointsOfUserId ($userId) {

		try {
			$joints = $this->_jointUsersInClass->getAllJointsOfUserId($userId);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg(sprintf($this->_languageManager->getText('errorNoJointUsersInClassOfUserId'),
					$userId));
		}
		catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointUsersInClass'));
		}
		if (isset($joints)) {
			return $joints;
		}
		else {
			return false;
		}
	}

	private function getJointUsersInClassByUserIdAndClassId ($userId, $classId) {

		try {
			$joint = $this->_jointUsersInClass->getJointOfUserIdAndClassId($userId, $classId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointUsersInClass'));
		}
		return $joint;
	}

	private function deleteJointUsersInClass ($jointId) {

		try {
			$this->_jointUsersInClass->deleteJoint($jointId);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorDeleteJointUsersInClass'));
		}
	}

	private function alterJointUsersInClass ($jointId, $statusName) {

		try {
			$status = $this->_usersInClassStatusManager->statusGetByName ($statusName);
			$this->_jointUsersInClass->alterStatusIdOfJoint($jointId, $status ['ID']);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAlterJointUsersInClass'));
		}
	}

	private function alterJointUsersInClassOfUserIdAndClassId ($userId, $classId, $statusName) {

		$joint = $this->getJointUsersInClassByUserIdAndClassId($userId, $classId);
		if ($statusName == 'noConnection') {
			$this->deleteJointUsersInClass($joint['ID']);
			$this->_interface->dieMsg($this->_languageManager->getText('finishedDeleteJointUsersInClass'));
		}
		else {
			try {
				$this->_usersInClassStatusManager->statusGetByName ($statusName);
			} catch (MySQLVoidDataException $e) {
				$this->_interface->dieError ($this->_languageManager->getText ('errorFetchUsersInClassStatus'));
			}
			$this->alterJointUsersInClass($joint['ID'], $statusName);
			$link = '<a href="index.php?section=Kuwasys|Classes&action=showClass">zurück</a>';
			$this->_interface->dieMsg($this->_languageManager->getText('finishedChangeJointUsersInClass') . '<br />' . $link);
		}
	}

	private function isClassMaxRegistrationReached ($classId) {
		$class = $this->_databaseAccessManager->classGet($classId);
		//Dont die when no joints where found
		$dbAccEMod = new DbAccExceptionMods (DbAccExceptionMods::$MySQLVoidDataException, DbAccExceptionMods::$ModDoNothing);
		$regOfClass = $this->_databaseAccessManager->dbAccessExec (KuwasysDatabaseAccess::JUserInClassManager, 'getAllJointsOfClassIdAndStatusActive', array($classId), __FUNCTION__, array($dbAccEMod));
		return ($class['maxRegistration'] >= count($regOfClass));
	}

	private function moveUserByClassToDatabase ($userId, $classIdOld, $classIdNew, $statusNew) {

		$jointUserInClassOld = $this->_databaseAccessManager->jointUserInClassGetByUserIdAndClassId($userId, $classIdOld);
		//checks if Joint is duplicated
		if($this->_databaseAccessManager->jointUserInClassIsExistingByUserIdAndClassId($userId, $classIdNew)) {
			$jointDuplicate = $this->_databaseAccessManager->jointUserInClassGetByUserIdAndClassId($userId, $classIdNew);
			//if IDs are the same, the same joint gets altered, so no duplication
			if($jointDuplicate ['ID'] != $jointUserInClassOld ['ID']) {
				$this->_interface->dieError($this->_languageManager->getText('moveUserToClassJointAlreadyExisting'));
			}
		}
		$this->_databaseAccessManager->jointUserInClassAlter($jointUserInClassOld ['ID'], $classIdNew, $userId, $statusNew);
	}

	/**-----------------------------------------------------------------------------
	 * Functions doing other stuff
	*----------------------------------------------------------------------------*/

	private function checkInputChangeUserData () {

		try {
			inputcheck($_POST['forename'], 'name', $this->_languageManager->getText('formForename'));
			inputcheck($_POST['name'], 'name', $this->_languageManager->getText('formName'));
			inputcheck($_POST['username'], 'name', $this->_languageManager->getText('formUsername'));
			inputcheck($_POST['email'], 'email', $this->_languageManager->getText('formEmail'));
			inputcheck($_POST['telephone'], 'number', $this->_languageManager->getText('formTelephone'));
		} catch (WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('formWrongInput'), $e->getFieldName()));
		}
		if ($_POST['password'] != '' && $_POST['password'] != NULL) {
			try {
				inputcheck($_POST['password'], 'password', $this->_languageManager->getText('formPassword'));
				inputcheck($_POST['passwordRepeat'], 'password', $this->_languageManager->getText('formPasswordRepeat'));
			} catch (WrongInputException $e) {
				$this->_interface->dieError(sprintf($this->_languageManager->getText('formWrongInput'), $e->getFieldName
						()));
			}
			$this->checkPasswordRepetition();
		}
	}

	/**
	 * @used-by Users::addUser
	 */
	private function checkAddUserInput () {

		try {
			inputcheck($_POST['forename'], 'name', $this->_languageManager->getText('formForename'));
			inputcheck($_POST['name'], 'name', $this->_languageManager->getText('formName'));
			if($_POST['username'] != '') {
				inputcheck($_POST['username'], 'name', $this->_languageManager->getText('formUsername'));
			}
			if($_POST['password'] != '') {
				inputcheck($_POST['password'], 'password', $this->_languageManager->getText('formPassword'));
				inputcheck($_POST['passwordRepeat'], 'password', $this->_languageManager->getText('formPasswordRepeat'));
			}
			if($_POST['email'] != '') {
				inputcheck($_POST['email'], 'email', $this->_languageManager->getText('formEmail'));
			}
			if($_POST['telephone'] != '') {
				inputcheck($_POST['telephone'], 'number',
					$this->_languageManager->getText('formTelephone'));
			}
		} catch (WrongInputException $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('formWrongInput'), $e->getFieldName()));
		}
	}

	/**
	 * @used-by Users::addUser
	 * @used-by Users:changeUser
	 */
	private function checkPasswordRepetition () {

		if ($_POST['password'] == $_POST['passwordRepeat']) {
			return true;
		}
		else {
			$this->_interface->dieError($this->_languageManager->getText('formWrongPasswordRepetition'));
		}
	}


	/**
	 * @used-by Users::addUserToDatabase
	 * Checks if the given Date is valid and returns a date-string in the
	 * format YYYY-MM-DD
	 * @param int(2) $day
	 * @param int(2) $month
	 * @param int(4) $year
	 */
	private function handleBirthday($day, $month, $year) {
		if(checkdate($month, $day, $year)) {
			$date = sprintf('%s-%s-%s', $year, $month, $day);
			return $date;
		}
		else {
			$this->_interface->dieError('Kein gültiges Datum eingegeben!');
		}
	}

	/**
	 * Sends an Email-Participation-Confirmation
	 */
	private function emailParticipationConfirmation() {
		UsersEmailParticipationConfirmation::init($this->_interface);
		UsersEmailParticipationConfirmation::execute();
	}


}
?>