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
	
	public function showAddUser() {
		
		$this->smarty->display($this->tplFilePath . 'addUser.tpl');
	}
	
	public function showAllUsers ($users) {
		
		$this->smarty->assign('users', $users);
		$this->smarty->display($this->tplFilePath . 'showUsers.tpl');
	}
	
	public function showChangeUser ($userData) {
		
		$this->smarty->assign('user', $userData);
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

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>