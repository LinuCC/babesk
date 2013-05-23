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
	
	public function GeneralSettings() {
		$this->smarty->display($this->tplFilePath . 'general.tpl');
	}
	
	public function LoanSettings($settings, $save) {
		$this->smarty->assign('settings', $settings);
		$this->smarty->assign('save', $save);
		$this->smarty->display($this->tplFilePath . 'loan.tpl');
	}
	
	public function RetourSettings() {
		$this->smarty->display($this->tplFilePath . 'retour.tpl');
	}
	
	public function TextSettings() {
		$this->smarty->display($this->tplFilePath . 'texts.tpl');
	}
	
	public function enableFormConfirm($enabled) {
		$this->smarty->assign('enabled', $enabled);
		$this->smarty->display($this->tplFilePath . 'enableConfirm.tpl');
	}
	
	public function enableFormConfirmFin() {
		$this->smarty->display($this->tplFilePath . 'enableConfirmFin.tpl');
	}
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>