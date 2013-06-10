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
	
	public function EditBankAccount($owner,$number,$blz,$institute) {
		$this->smarty->assign('owner', $owner);
		$this->smarty->assign('number', $number);
		$this->smarty->assign('blz', $blz);
		$this->smarty->assign('institute', $institute);
		$this->smarty->display($this->tplFilePath . 'editBankAccount.tpl');
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
	
	public function EditCoverLetter($title, $text) {
		$this->smarty->assign('title', $title);
		$this->smarty->assign('text', $text);
		$this->smarty->display($this->tplFilePath . 'editCoverLetter.tpl');
	}
	
	public function SavingSuccess() {
		$this->smarty->display($this->tplFilePath . 'saveSuccess.tpl');
	}
	
	public function SavingFailed() {
		$this->smarty->display($this->tplFilePath . 'saveFailed.tpl');
	}
	
	public function enableFormConfirm($enabled) {
		$this->smarty->assign('enabled', $enabled);
		$this->smarty->display($this->tplFilePath . 'enableConfirm.tpl');
	}
	
	public function enableFormConfirmFin() {
		$this->smarty->display($this->tplFilePath . 'enableConfirmFin.tpl');
	}
	
	public function ShowPreviewInfoTexts() {
		$this->smarty->display($this->tplFilePath . 'showPreviewInfoTexts.tpl');
	}
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>