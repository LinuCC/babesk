<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/functions.php';

class EmailConfiguration extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct ($name, $display_name, $path) {
		parent::__construct ($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/** The entry-point of the Module
	 *
	 */
	public function execute ($dataContainer) {
		$this->entryPoint ($dataContainer);
		if (isset($_GET ['action'])) {
			switch ($_GET ['action']) {
				case 'changeData':
					$this->changeSettings ();
			}
		}
		else {
			$this->mainMenuShow ();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/** Sets the Classes Variables
	 *
	 */
	protected function entryPoint ($dataContainer) {
		defined('_AEXEC') or die('Access denied');

		require_once 'EmailConfigurationInterface.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';

		$this->_interface = new EmailConfigurationInterface ($this->relPath, $dataContainer->getSmarty());
		$this->_globalSettingsManager = new GlobalSettingsManager ();
	}

	private function mainMenuShow () {

		$host = $this->globalSettingGetWithoutDieing ('SMTP_HOST');
		$username = $this->globalSettingGetWithoutDieing ('SMTP_USERNAME');
		$password = $this->globalSettingGetWithoutDieing ('SMTP_PASSWORD');
		$fromName = $this->globalSettingGetWithoutDieing ('SMTP_FROMNAME');
		$from = $this->globalSettingGetWithoutDieing ('SMTP_FROM');
		$this->_interface->mainMenuDisplay ($host, $username, $password, $fromName, $from);
	}

	/** Fetches a GlobalSetting, but without dieing
	 * Fetches a GlobalSetting with the name $name from the database
	 * (name defined in GlobalSettings) and returns it. If an MySQLVoidData
	 * error occurred, it will return a void string.
	 */
	private function globalSettingGetWithoutDieing ($name) {
		try {
			$value = $this->_globalSettingsManager->valueGet (constant('GlobalSettings::' . $name));
		} catch (Exception $e) {
			return '';
		}
		return $value;
	}

	private function changeSettings () {
		$this->changeSettingsCheckInput ();
		$this->changeSettingsInDatabase ();
		$this->_interface->dieMsg ('Die Emaileinstellungen wurden erfolgreich verändert.');
	}

	private function changeSettingsCheckInput () {
		try {
			inputcheck ($_POST ['from'], 'email', 'Absender');
			if($_POST ['fromName'] == '') {
				throw new WrongInputException ('fromName void', 'Absender');
			}
			if($_POST ['password'] == '') {
				throw new WrongInputException ('password void', 'Passwort');
			}
			if($_POST ['username'] == '') {
				throw new WrongInputException ('username void', 'Benutzername');
			}
			if($_POST ['host'] == '') {
				throw new WrongInputException ('host void', 'Host');
			}
		} catch (WrongInputException $e) {
			$this->_interface->dieError (sprintf ('Im Feld %s wurden falsche Daten eingegeben.', $e->getFieldName()));
		}
	}

	private function changeSettingsInDatabase () {
		$this->_globalSettingsManager->valueSet (GlobalSettings::SMTP_HOST, $_POST ['host']);
		$this->_globalSettingsManager->valueSet (GlobalSettings::SMTP_USERNAME, $_POST ['username']);
		$this->_globalSettingsManager->valueSet (GlobalSettings::SMTP_PASSWORD, $_POST ['password']);
		$this->_globalSettingsManager->valueSet (GlobalSettings::SMTP_FROMNAME, $_POST ['fromName']);
		$this->_globalSettingsManager->valueSet (GlobalSettings::SMTP_FROM, $_POST ['from']);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_interface;
	private $_globalSettingsManager;
}

?>
