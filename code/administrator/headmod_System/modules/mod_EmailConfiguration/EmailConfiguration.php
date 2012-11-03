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
	private function entryPoint ($dataContainer) {
		defined('_AEXEC') or die('Access denied');

		require_once 'EmailConfigurationInterface.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';

		$this->_interface = new EmailConfigurationInterface ($this->relPath, $dataContainer->getSmarty());
		$this->_globalSettingsManager = new GlobalSettingsManager ();
	}

	private function mainMenuShow () {
		$host = $this->_globalSettingsManager->valueGet (GlobalSettings::SMTP_HOST);
		$username = $this->_globalSettingsManager->valueGet (GlobalSettings::SMTP_USERNAME);
		$password = $this->_globalSettingsManager->valueGet (GlobalSettings::SMTP_PASSWORD);
		$fromName = $this->_globalSettingsManager->valueGet (GlobalSettings::SMTP_FROMNAME);
		$from = $this->_globalSettingsManager->valueGet (GlobalSettings::SMTP_FROM);
		$this->_interface->mainMenuDisplay ($host, $username, $password, $fromName, $from);
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