<?php
require_once PATH_ACCESS . '/GlobalSettingsManager.php';

class AdminHelpProcessing {
	function __construct($helpInterface) {
		$this->msg = array('ERR_CHANGE' => 'Ein Fehler ist beim ändern des Textes aufgetreten');
		$this->helpManager = new GlobalSettingsManager();
		$this->helpInterface = $helpInterface;
	}

	/**
	 * Changes the help-text (shown to the users) to the given string
	 * Enter description here ...
	 * @param string $str
	 */
	function change_help($str) {
		if (!$str || $str = '')
			$str = '&nbsp;';

		try {
			$this->helpManager->changeHelpText(mysql_escape_string($str));
		} catch (Exception $e) {
			$this->helpInterface
					->dieError($this->msg['ERR_CHANGE'] . '<br>Fehlermeldung:' . $e->getMessage());
			die();
		}
		$this->helpInterface->EditHelpFin();
	}

	protected $msg;

	protected $helpManager;

	protected $helpInterface;
}
?>