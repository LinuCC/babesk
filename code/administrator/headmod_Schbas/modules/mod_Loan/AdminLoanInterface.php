<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * @author Mirek Hancl
 *
 */
class AdminLoanInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_loan_header.tpl';
		$this->smarty->assign('loanParent', $this->MOD_HEADING);
	}
	
	public function CardId() {
		$this->smarty->display($this->tplFilePath . 'form.tpl');
	}
	
	public function ShowLoanBooks($data, $card_id, $uid,$fullname,$alert) {
		$this->smarty->assign('cardid', $card_id);
		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('adress', ($_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
		$this->smarty->assign('data', $data);
		$this->smarty->assign('fullname',$fullname);
		$this->smarty->assign('alert',$alert);
		$this->smarty->display($this->tplFilePath . 'loanbooks.tpl');
	}
	
	public function ShowLoanBooksAjax($data,$card_id,$uid,$fullname) {
		$this->smarty->assign('cardid', $card_id);
		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('adress', ($_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
		$this->smarty->assign('data', $data);
		$this->smarty->assign('fullname',$fullname);
		$this->smarty->display($this->tplFilePath . 'loanbooksAjax.tpl');
	}
	
	public function LoanEmpty() {
		$this->smarty->assign('adress', ($_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI']);
		$this->smarty->display($this->tplFilePath . 'loanbooksAjaxEmpty.tpl');
	}
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>