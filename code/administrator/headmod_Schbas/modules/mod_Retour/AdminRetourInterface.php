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
	
	public function ShowRetourBooks($data,$uid) {
		$this->smarty->assign('uid', $uid);
		$this->smarty->assign('path', dirname($_SERVER['SCRIPT_NAME']).'/headmod_Schbas/modules/mod_Retour');
		$this->smarty->assign('data', $data);
		$this->smarty->display($this->tplFilePath . 'retourbooks.tpl');
	}
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>