<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/functions.php';
require_once 'UsersInterface.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysGradeManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInGrade.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInSchoolYear.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysSchoolYearManager.php';

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
				case 'showUsers':
					$this->showUsers();
					break;
				case 'deleteUser':
					$this->deleteUser();
					break;
				case 'changeUser':
					$this->changeUserData();
					break;
				default:
					$this->_interface->dieError($this->_languageManager->getText('actionValueWrong'));
			}

		}
		else {
			$this->showMainMenu();
		}

	}

	////////////////////////////////////////////////////////////////////////////////
	//Implements
	private function entryPoint ($dataContainer) {

		defined('_AEXEC') or die('Access denied');
		$this->_usersManager = new KuwasysUsersManager();
		$this->_gradeManager = new KuwasysGradeManager();
		$this->_schoolYearManager = new KuwasysSchoolYearManager();
		$this->_jointUsersInGradeManager = new KuwasysJointUsersInGrade();
		$this->_jointUsersInSchoolYear = new KuwasysJointUsersInSchoolYear();
		$this->_interface = new UsersInterface($this->relPath, $dataContainer->getSmarty());
		$this->_languageManager = $dataContainer->getLanguageManager();
		$this->_languageManager->setModule('Users');
	}

	/**-----------------------------------------------------------------------------
	 * Entry-point-functions for different parts of the module
	 *----------------------------------------------------------------------------*/

	/**
	 * adds a User to the MySQL-table
	 */
	private function addUser () {

		if (isset($_POST['username'], $_POST['name'], $_POST['forename'], $_POST['telephone'])) {
			$this->checkAddUserInput();
			$this->checkPasswordRepetition();
			$this->addUserToDatabase();
			$this->addJointUsersInSchoolYearByAddUser();
			$userID = $this->_usersManager->getLastInsertedID();
			$this->addJointUsersInGrade($userID, $_POST['grade']);
			$this->_interface->dieMsg(sprintf($this->_languageManager->getText('finishedAddUser'), $_POST['forename'],
				$_POST['name']));
		}
		else {
			$this->showAddUser();
		}
	}

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
		$this->_interface->showAllUsers($users);
	}

	private function showMainMenu () {

		$this->_interface->showMainMenu();
	}

	private function showAddUser () {

		$grades = $this->getAllGrades();
		$schoolYears = $this->getAllSchoolYears();
		$this->_interface->showAddUser($grades, $schoolYears);
	}

	/**-----------------------------------------------------------------------------
	 * Functions for accessing Database-tables and dieing when error occuring
	 *----------------------------------------------------------------------------*/

	/********************
	 * UserManager
	 ********************/

	/**
	 * @used-by Users::addUser
	 */
	private function addUserToDatabase () {

		$date = $this->convertNumbersToDate($_POST['Date_Day'], $_POST['Date_Month'], $_POST['Date_Year']);
		try {
			$this->_usersManager->addUser($_POST['forename'], $_POST['name'], $_POST['username'], hash_password($_POST[
				'password']), $_POST['email'], $_POST['telephone'], $date);
		} catch (MySQLConnectionException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddUserConnectDatabase'));
		}
		catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('errorAddUser'), $e->getMessage()));
		}
	}

	private function ChangeUserDataToDatabase () {

		if ($_POST['password'] != '' && $_POST['password'] != NULL) {

			$this->_usersManager->changeUserWithoutPassword($_POST['ID'], $_POST['forename'], $_POST['name'], $_POST[
				'username'], $_POST['email'], $_POST['telephone']);
		}
		else {

			$this->_usersManager->changeUserWithoutPassword($_GET['ID'], $_POST['forename'], $_POST['name'], $_POST[
				'username'], $_POST['email'], $_POST['telephone'], hash_password($_POST['password']));
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

		try {
			$this->_jointUsersInGradeManager->addJoint($userID, $gradeID);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorAddJointUsersInGrade'));
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
		return $joints;
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
		return $users;
	}

	private function getGradeByGradeId ($gradeID) {

		try {
			$grade = $this->_gradeManager->getGrade($gradeID);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchGrade'));
		}
		return $grade;
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
		} catch (MySQLVoidDataException $e) {
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
		} catch (Exception $e) {
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
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointUsersInSchoolYear'));
		}
		return $schoolyearID;
	}
	
	private function setUpUserValueSelectedSchoolyear ($user) {
	
		$schoolyearId = $this->getSchoolyearIdOfUserId($user ['ID']);
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

		$userID = $this->_usersManager->getLastInsertedID();
		$this->addJointUsersInSchoolYear($userID, $_POST['schoolyear']);
	}
	
	/**
	 * changes entries of the Table jointUsersInSchoolyear according to the values the User has given in the form changeUser
	 */
	private function changeJointUsersInSchoolYearByChangeUser () {
		
		$schoolyearBeforeId = $this->getSchoolyearIdOfUserId($_GET['ID']);
		
		if(!isset($_POST['schoolyear']) || $_POST['schoolyear'] == 0) {
			$this->_interface->dieError($this->_languageManager->getText('errorInputJointUsersInSchoolYear'));
		}
		
		if($_POST['schoolyear'] != $schoolyearBeforeId) {
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
	
		$jointsUsersInSchoolyear = $this->get();
		$grades = $this->getAllGrades();
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
		return $users;
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
			inputcheck($_POST['username'], 'name', $this->_languageManager->getText('formUsername'));
			inputcheck($_POST['password'], 'password', $this->_languageManager->getText('formPassword'));
			inputcheck($_POST['passwordRepeat'], 'password', $this->_languageManager->getText('formPasswordRepeat'));
			inputcheck($_POST['email'], 'email', $this->_languageManager->getText('formEmail'));
			inputcheck($_POST['telephone'], 'number', $this->_languageManager->getText('formTelephone'));
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
	 * @param int(2) $day
	 * @param int(2) $month
	 * @param int(4) $year
	 */
	private function convertNumbersToDate ($day, $month, $year) {

		$date = sprintf('%s.%s.%s', $day, $month, $year);
		return $date;
	}
}
?>