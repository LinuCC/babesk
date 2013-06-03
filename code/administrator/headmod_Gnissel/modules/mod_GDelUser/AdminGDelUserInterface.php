<?php

class AdminGDelUserInterface extends AdminInterface {
	
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
	public function ShowDelUser($uid,$userData) {
		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('name', $userData['name']);
		$this->smarty->assign('forename', $userData['forename']);
		$this->smarty->assign('class', $userData['class']);
		
		$this->smarty->display($this->tplFilePath  . '/delUser.tpl');
	}
	
	public function CardId() {
		$this->smarty->display($this->tplFilePath . '/scanCard.tpl');
	}
	
	function ShowDeleteFin($uid) {
		
		$this->smarty->assign('pdf','../include/pdf/tempPdf/deleted_'.$uid.'.pdf');
		$this->smarty->assign('uid',$uid);
		$this->smarty->display($this->tplFilePath.'/deletion_finished.tpl');
	}
	
	public function showDeletePdfSuccess () {
		$this->smarty->display ($this->tplFilePath . '/showDeletePdfSuccess.tpl');
	}
	
}

?>