<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class NachrichtenUsersInterface extends AdminInterface {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($modPath, $smarty) {

		parent::__construct($modPath, $smarty);
		$this->parentPath = $this->tplFilePath . 'header.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function showMainMenu () {

		$this->smarty->display($this->tplFilePath . 'mainMenu.tpl');
	}

	public function showAddUser($grades, $schoolYears) {

		$this->smarty->assign('grades', $grades);
		$this->smarty->assign('schoolyears', $schoolYears);
		$this->smarty->display($this->tplFilePath . 'addUser.tpl');
	}

	public function showSelectCsvFileForImport () {

		$this->smarty->display($this->tplFilePath . 'importLocalCsvFile.tpl');
	}

	public function showAllUsers ($users) {

		$this->smarty->assign('users', $users);
		$this->smarty->display($this->tplFilePath . 'showUsers.tpl');
	}

	public function showChangeUser ($userData, $grades, $schoolyears) {

		$this->smarty->assign('user', $userData);
		$this->smarty->assign('grades', $grades);
		$this->smarty->assign('schoolyears', $schoolyears);
		$this->smarty->assign('pathToJavascript', $this->tplFilePath . 'showUsers.js');
		$this->smarty->display($this->tplFilePath . 'changeUser.tpl');
	}

	public function showDeleteUserConfirmation ( $ID, $userForename, $userName, $languageManager) {

		$promptMessage = sprintf($languageManager->getText('confirmDeleteUser'), $userForename, $userName);
		$sectionString = 'Kuwasys|Users';
		$actionString = 'deleteUser&ID=' . $ID;
		$confirmedString = $languageManager->getText('confirmDeleteUserYes');
		$notConfirmedString = $languageManager->getText('confirmDeleteUserNo');
		$this->confirmationDialog($promptMessage, $sectionString, $actionString, $confirmedString, $notConfirmedString);
	}

	public function showAddUserToClassDialog ($user, $classes) {

		$this->smarty->assign('user', $user);
		$this->smarty->assign('classes', $classes);
		$this->smarty->display($this->tplFilePath . 'addUserToClass.tpl');
	}

	public function showChangeUserToClassDialog ($user, $class, $linkStatus) {

		$this->smarty->assign('user', $user);
		$this->smarty->assign('class', $class);
		$this->smarty->assign('linkStatus', $linkStatus);
		$this->smarty->display($this->tplFilePath . 'changeUserToClass.tpl');

	}

	public function showUserDetails ($user) {

		$this->smarty->assign('user', $user);
		$this->smarty->display($this->tplFilePath . 'showUserDetails.tpl');
	}

	public function showUsersWaiting ($adjustedUsers) {

		$this->smarty->assign('users', $adjustedUsers);
		$this->smarty->display($this->tplFilePath . 'showUsersWaiting.tpl');
	}

	public function showMoveUserByClass ($classOld, $user, $classes, $statusArray) {

		$this->smarty->assign('classes', $classes);
		$this->smarty->assign('statusArray', $statusArray);
		$this->smarty->assign('classOld', $classOld);
		$this->smarty->assign('user', $user);
		$this->smarty->display($this->tplFilePath . 'moveUserToClass.tpl');
	}

	public function showMoveUserByClassClassFullConfirmation ($user, $classOld, $classNew, $statusNew) {

		$this->smarty->assign('classOld', $classOld);
		$this->smarty->assign('classNew', $classNew);
		$this->smarty->assign('user', $user);
		$this->smarty->assign('statusNew', $statusNew);
		$this->smarty->display($this->tplFilePath . 'moveUserToClassClassFullConfirmation.tpl');
	}

	public function showUsersGroupedByYearAndGrade ($schoolyears, $schoolyearDesired, $grades, $gradeDesired, $users) {

		$this->smarty->assign('schoolyearAll', $schoolyears);
		$this->smarty->assign('schoolyearDesired', $schoolyearDesired);
		$this->smarty->assign('gradeAll', $grades);
		$this->smarty->assign('gradeDesired', $gradeDesired);
		$this->smarty->assign('users', $users);
		$this->smarty->display($this->tplFilePath . 'showUsersGroupedByYearAndGrade.tpl');
	}

	public function showMainDialogResetPasswordOfAllUsers ($activeYearName) {
		$this->smarty->assign ('activeYearName', $activeYearName);
		$this->smarty->display ($this->tplFilePath . 'resetPasswordOfAll.tpl');
	}

	public function showConfirmResetPasswordOfAllUsers ($activeYearName) {
		$this->smarty->assign ('activeYearName', $activeYearName);
		$this->smarty->display ($this->tplFilePath . 'confirmResetPasswordOfAll.tpl');
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>