<?php
class AdminSchbasSettingsProcessing {


	var $messages = array();
	private $RetourInterface;

	function __construct($SchbasSettingsInterface) {

		$this->SchbasSettingsInterface = $SchbasSettingsInterface;
		$this->msg = array();
	}

	/**
	 * Ausleihtabelle anzeigen
	 */
	function GetLoanSettings() {
		$settings = TableMng::query("SELECT * FROM schbas_fee");
		return $settings;
	}
}

?>
