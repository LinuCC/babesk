<?php
class AdminSchbasSettingsProcessing {


	var $messages = array();
	private $RetourInterface;

	protected $logs;

	function __construct($SchbasSettingsInterface) {

		$this->SchbasSettingsInterface = $SchbasSettingsInterface;
		global $logger;
		$this->logs = $logger;
		$this->msg = array();
	}

	/**
	 * Ausleihtabelle anzeigen
	 */
	function GetLoanSettings() {
		$settings = TableMng::query("SELECT * FROM schbas_fee", true);
		return $settings;
	}
}

?>