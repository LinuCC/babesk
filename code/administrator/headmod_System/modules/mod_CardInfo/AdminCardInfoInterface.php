<?php

class AdminCardInfoInterface extends AdminInterface {
	
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
	public function ShowCardInfo($userData,$cardID) {
		$this->smarty->assign('cardID', $cardID);
		$this->smarty->assign('name', $userData['name']);
		$this->smarty->assign('forename', $userData['forename']);
		$this->smarty->assign('class', $userData['class']);
		$this->smarty->display($this->tplFilePath  . '/result.tpl');
	}
	
	public function CardId() {
		$this->smarty->display($this->tplFilePath . '/form.tpl');
	}
	
}

?>