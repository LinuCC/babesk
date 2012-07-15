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
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>