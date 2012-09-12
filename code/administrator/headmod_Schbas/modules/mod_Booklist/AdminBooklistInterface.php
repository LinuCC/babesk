<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * @author Mirek Hancl
 *
 */
class AdminBooklistInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_booklist_header.tpl';
		$this->smarty->assign('booklistParent', $this->MOD_HEADING);
	}

	function ShowSelectionFunctionality($arr_action) {
		$this->smarty->assign('action', $arr_action);
		$this->smarty->display($this->tplFilePath.'index.tpl');
	}
	
	function ShowBooklist($bookcodes) {
		$this->smarty->assign('bookcodes', $bookcodes);
		$this->smarty->display($this->tplFilePath.'show_booklist.tpl');
	}
	
	function ShowChangeBook($bookdata) {
		$this->smarty->assign('bookdata', $bookdata);
		$this->smarty->display($this->tplFilePath.'change_book.tpl');
	}
	
	function ShowChangeBookFin($id, $subject, $class, $title, $author, $publisher, $isbn, $price, $bundle) {
		$this->smarty->assign('id', $id);
		$this->smarty->assign('subject', $subject);
		$this->smarty->assign('class', $class);
		$this->smarty->assign('title', $title);
		$this->smarty->assign('author', $author);
		$this->smarty->assign('publisher', $publisher);
		$this->smarty->assign('isbn', $isbn);
		$this->smarty->assign('price', $price);
		$this->smarty->assign('bundle', $bundle);
		$this->smarty->display($this->tplFilePath.'change_book_fin.tpl');
	}
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>