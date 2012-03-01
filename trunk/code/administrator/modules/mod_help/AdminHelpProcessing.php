<?php
require_once PATH_INCLUDE . '/global_settings_access.php';

class AdminHelpProcessing {
	function __construct() {
		$this->msg = array('ERR_CHANGE' => 'Ein Fehler ist beim ändern des Textes aufgetreten');
		$this->helpManager = new GlobalSettingsManager();
		$this->helpInterface = new AdminHelpInterface();
	}
	
	/**
	 * Changes the help-text (shown to the users) to the given string
	 * Enter description here ...
	 * @param string $str
	 */
	function change_help($str) {
		try {
			$this->helpManager->changeHelpText(mysql_escape_string($str));
		} catch (Exception $e) {
			$this->helpInterface->ShowError($this->msg['ERR_CHANGE'] . '<br>Fehlermeldung:' . $e->getMessage());
			die();
		}
		$this->helpInterface->EditHelpFin();
	}
	
	protected $msg;
	
	protected $helpManager;
	
	protected $helpInterface;
}
?>