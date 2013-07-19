<?php

class AdminBookInfoInterface extends AdminInterface {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($folder_path) {
		
		parent::__construct($folder_path);
		
		$this->parentPath = $this->tplFilePath  . 'mod_bookinfo_header.tpl';
		$this->smarty->assign('checkoutParent', $this->parentPath);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * Shows some generic user infos
	 */	
	public function ShowBookInfo($userData,$bookData) {
		$this->smarty->assign('userID', $userData['ID']);
		$this->smarty->assign('name', $userData['name']);
		$this->smarty->assign('forename', $userData['forename']);
		$this->smarty->assign('class', $userData['class']);
		$this->smarty->assign('locked', $userData['locked']);
		$this->smarty->assign('bookID', $bookData['id']);
		$this->smarty->assign('subject', $bookData['subject']);
		$this->smarty->assign('class', $bookData['class']);
		$this->smarty->assign('title', $bookData['title']);
		$this->smarty->assign('author', $bookData['author']);
		$this->smarty->assign('publisher', $bookData['publisher']);
		$this->smarty->display($this->tplFilePath  . '/result.tpl');
	}
	
	public function BookId() {
		$this->smarty->display($this->tplFilePath . '/form.tpl');
	}
	
}

?>