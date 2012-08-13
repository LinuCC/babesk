<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * @author Mirek Hancl
 *
 */
class AdminRetourInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_retour_header.tpl';
		$this->smarty->assign('retourParent', $this->MOD_HEADING);
	}
	
	public function CardId() {
		$this->smarty->display($this->tplFilePath . 'form.tpl');
	}
	
	public function ShowRetourBooks($data,$card_id,$uid) {
		$this->smarty->assign('cardid', $card_id);
		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('adress', ($_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
		$this->smarty->assign('data', $data);
		$this->smarty->display($this->tplFilePath . 'retourbooks.tpl');
	}
	
	public function ShowRetourBooksAjax($data,$card_id,$uid) {
		$this->smarty->assign('cardid', $card_id);
		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('adress', ($_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
		$this->smarty->assign('data', $data);
		$this->smarty->display($this->tplFilePath . 'retourbooksAjax.tpl');
	}
	
	public function RetourEmpty() {
		$this->smarty->assign('adress', ($_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
		$this->smarty->display($this->tplFilePath . 'retourbooksAjaxEmpty.tpl');
	}
	
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>