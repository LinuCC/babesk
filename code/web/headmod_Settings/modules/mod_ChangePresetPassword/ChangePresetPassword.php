<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/WebInterface.php';
require_once PATH_ACCESS . '/UserManager.php';
require_once PATH_ACCESS . '/GlobalSettingsManager.php';

class ChangePresetPassword extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		$this->init ();
		$this->actionDetermine ();
	}

	private function init () {
		//No direct access
		defined('_WEXEC') or die("Access denied");
		global $smarty;
		$this->_smarty = $smarty;
		$this->_interface = new WebInterface ($this->_smarty);
		$this->_userManager = new UserManager ();
		$this->_globalSettingsManager = new GlobalSettingsManager ();
		$this->firstLoginCheck ();
	}

	private function actionDetermine () {
		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'changePassword':
					$this->changePasswordHandle ();
					break;
				default:
					die ('Wrong variable for action!');
			}
		}
		else {
			$this->dialogChangePasswordShow ();
		}
	}

	/** Checks if the Module gets called after the user has changed the Password
	 * Its function is to disallow access to this module at a later time, with the
	 * links linking to this module hacked into the browser
	 */
	private function firstLoginCheck () {
		$user = $this->_userManager->getUserData ($_SESSION ['uid']);
		if (!$user ['first_passwd']) {
			$this->_interface->DieError ('Kein Zugriff auf dieses Modul; Dass Passwort wurde schon einmal geändert');
		}
	}

	private function dialogChangePasswordShow () {
		$this->_smarty->display ($this->_smartyPath . 'changePasswordDialog.tpl');
	}

	private function changePasswordHandle () {
		$pw = $_POST ['newPassword'];
		$repeatPw = $_POST ['newPasswordRepeat'];
		$this->changePasswordCheckInput ($pw, $repeatPw);
		$this->changePasswordToDatabase ($pw);
		$this->_interface->DieMessage ('Das Passwort wurde verändert.');
	}

	private function changePasswordCheckInput ($pw, $repeatPw) {
		if ($pw !== $repeatPw) {
			$this->_interface->DieError ('Das eingegebene Passwort stimmt nicht mit der Wiederholung überein!');
		}
		try {
			inputcheck ($pw, 'password');
		} catch (WrongInputException $e) {
			$this->_interface->DieError ('Das Passwort enthält nicht unterstützte Zeichen oder ist zu kurz / zu lang!');
		}
		try {
			$presetPw = $this->_globalSettingsManager->valueGet (GlobalSettings::PRESET_PASSWORD);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->DieError ('Konnte das voreingestellte Passwort nicht abrufen');
		}
		if ($presetPw == hash_password($pw)) {
			$this->_interface->DieError ('Dass Passwort ist das gleiche wie das voreingestellte! Nimm ein anderes!');
		}
	}

	private function changePasswordToDatabase ($pw) {
		try {
			$this->_userManager->changePassword ($_SESSION['uid'] ,$pw);
		} catch (Exception $e) {
			$this->_interface->DieError ('Konnte das Passwort nicht verändern!');
		}
		try {
			$this->_userManager->setFirstPasswordOfUser ($_SESSION ['uid'], false);
		} catch (Exception $e) {
			$this->_interface->DieError ('Konnte die Passwortänderung nicht speichern! Bitte kontaktiere den Administrator!');
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $_smarty;
	private $_smartyPath;
	private $_interface;
	private $_userManager;
	private $_globalSettingsManager;
}

?>