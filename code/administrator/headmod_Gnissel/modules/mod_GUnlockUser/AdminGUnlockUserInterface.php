<?php

class AdminGUnlockUserInterface extends AdminInterface {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($folder_path) {
		
		parent::__construct($folder_path);
		
		$this->parentPath = $this->tplFilePath  . 'mod_cardinfo_header.tpl';
		$this->smarty->assign('checkoutParent', $this->parentPath);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * Shows some generic user infos
	 */	
	public function ShowUnlockUser($uid,$userData) {
		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('name', $userData['name']);
		$this->smarty->assign('forename', $userData['forename']);
		$this->smarty->assign('class', $userData['class']);
		
		$this->smarty->display($this->tplFilePath  . '/unlockUser.tpl');
	}
	
	public function CardId() {
		$this->smarty->display($this->tplFilePath . '/scanCard.tpl');
	}
	
}

?>