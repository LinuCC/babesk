<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class UsersInterface extends AdminInterface {
	
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
	
	public function showMoveUserByClass ($classIdOld, $classes, $statusArray) {
		
		$this->smarty->assign('classes', $classes);
		$this->smarty->assign('statusArray', $statusArray);
		$this->smarty->assign('classIdOld', $classIdOld);
		
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>