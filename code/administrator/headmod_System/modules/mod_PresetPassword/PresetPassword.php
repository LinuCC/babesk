<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/functions.php';
require_once PATH_ACCESS . '/GlobalSettingsManager.php';
require_once 'PresetPasswordInterface.php';
require_once PATH_ADMIN . '/headmod_System/System.php';

class PresetPassword extends System {

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
				case 'changePassword':
					$changePwChecked = $this->changePasswordHandle ();
					$this->changeEmailHandle ($changePwChecked);
					$this->_interface->dieMsg ('Die Einstellungen wurden übernommen');
					break;
			default:
				die ('action not defined');
			}
		}
		else {
			$this->mainMenuShow ();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Sets the Classes Variables
	 */
	protected function entryPoint ($dataContainer) {
		defined('_AEXEC') or die('Access denied');
		$this->_interface = new PresetPasswordInterface ($this->relPath, $dataContainer->getSmarty());
		$this->_globalSettingsManager = new GlobalSettingsManager ();
	}

	private function mainMenuShow () {
		$firstLoginChangePassword = $this->firstLoginChangePasswordGet ();
		$onFirstLoginChangeEmail = $this->emailChangeOnFirstLoginGet ();
		$this->_interface->mainMenuShow ($firstLoginChangePassword, $onFirstLoginChangeEmail);
	}

	private function changePasswordHandle () {
		$pw = $_POST ['newPassword'];
		$onFirstLoginChangePassword = (isset($_POST ['firstLoginPassword'])) ? '1' : '0';
		$this->changePasswordCheckInput ($pw);
		$this->presetPasswordSet ($pw);
		$this->firstLoginChangePasswordSet ($onFirstLoginChangePassword);
		return ($pw != '0');
	}

	private function changeEmailHandle ($isAllowed) {
		$onFirstLoginChangeEmail = (isset($_POST ['firstLoginEmail'])) ? '1' : '0';
		$onFirstLoginChangeEmailForce = (isset($_POST ['firstLoginEmailForce'])) ? '1' : '0';
		if (!$isAllowed && $onFirstLoginChangeEmail == '1') {
			$this->_interface->dieError ('Die Email kann nur abgefragt werden, wenn auch das Passwort beim ersten Login abgefragt wird');
		}
		$this->emailChangeOnFirstLoginSet ($onFirstLoginChangeEmail);
		$this->emailChangeOnFirstLoginForcedSet ($onFirstLoginChangeEmailForce);
	}

	private function changePasswordCheckInput ($pw) {
		try {
			inputcheck ($pw, 'password');
		} catch (WrongInputException $e) {
			$this->_interface->dieError ('Es wurde ein falsches Passwort eingegeben'
			);
		}
	}

	private function presetPasswordGet () {
		try {
			$password = $this->_globalSettingsManager->valueGet (GlobalSettings::PRESET_PASSWORD);
		} catch (MySQLVoidDataException $e) {
			$this->_globalSettingsManager->valueSet (GlobalSettings::PRESET_PASSWORD, hash_password(''));
			$password = hash_password('');
		}
		return $password;
	}

	private function presetPasswordSet ($password) {
		try {
			$this->_globalSettingsManager->valueSet (GlobalSettings::PRESET_PASSWORD, hash_password($password));
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte das Passwort nicht verändern');
		}
	}

	private function firstLoginChangePasswordGet () {
		try {
			$flcp = $this->_globalSettingsManager->valueGet (GlobalSettings::FIRST_LOGIN_CHANGE_PASSWORD);
		} catch (MySQLVoidDataException $e) {
			$this->_globalSettingsManager->valueSet (GlobalSettings::FIRST_LOGIN_CHANGE_PASSWORD, '0');
			$flcp = '0';
		}
		return $flcp;
	}

	private function firstLoginChangePasswordSet ($flcp) {
		try {
			$this->_globalSettingsManager->valueSet (GlobalSettings::FIRST_LOGIN_CHANGE_PASSWORD, $flcp);
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte das den Wert für die Funktion zum Verändern des Passwortes bei einem ersten Login nicht verändern');
		}
	}

	/**
	 * Returns if the Email should be changed by the User on his first login
	 * @return boolean
	 */
	private function emailChangeOnFirstLoginGet () {
		try {
			$bEmail = $this->_globalSettingsManager->valueGet (GlobalSettings::FIRST_LOGIN_CHANGE_EMAIL);
		} catch (MySQLVoidDataException $e) {
			$this->emailChangeOnFirstLoginSet ('0');
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte den Wert für das Verändern der Email bei einem ersten Userlogin nicht abrufen');
		}
		return $bEmail;
	}

	private function emailChangeOnFirstLoginSet ($onFirstLoginChangeEmail) {
		try {
			$this->_globalSettingsManager->valueSet (GlobalSettings::FIRST_LOGIN_CHANGE_EMAIL, $onFirstLoginChangeEmail);
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte den Wert für das Verändern der Email bei einem ersten Userlogin nicht abrufen');
		}
	}

	protected function emailChangeOnFirstLoginForcedSet ($isForced) {
		try {
			$this->_globalSettingsManager->valueSet (GlobalSettings::FIRST_LOGIN_CHANGE_EMAIL_FORCED, $isForced);
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte den Wert für das Verändern der Email bei einem ersten Userlogin nicht abrufen');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_interface;
	protected $_globalSettingsManager;
}

?>
