<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * @author Jan Feuchter
 *
 */
class AdminSchbasSettingsInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_schbasSettings_header.tpl';
		$this->smarty->assign('schbasSettingsParent', $this->MOD_HEADING);
	}
	
	public function InitialMenu() {
		$this->smarty->display($this->tplFilePath . 'index.tpl');
	}
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>