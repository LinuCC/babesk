<?php

require_once PATH_WEB . '/Settings/Settings.php';

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/WebInterface.php';
require_once PATH_ACCESS . '/UserManager.php';
require_once PATH_ACCESS . '/GlobalSettingsManager.php';

class ChangePresetPassword extends Settings {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY_TPL . '/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->init($dataContainer);
		$this->actionDetermine();
	}

	private function init($dataContainer) {

		//No direct access
		defined('_WEXEC') or die("Access denied");

		$this->_smarty = $dataContainer->getSmarty();
		$this->_interface = new WebInterface($this->_smarty);
		$this->_userManager = new UserManager();
		$this->_globalSettingsManager = new GlobalSettingsManager();
		$this->firstLoginCheck();
	}

	private function actionDetermine() {
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'changePassword':
					$this->changePasswordHandle();
					$this->emailChangeOnFirstLoginHandle();
					$this->isFirstPasswordOfUserSet(false);
					$this->_interface->DieMessage('Die Einstellungen wurden übernommen.');
					break;
				default:
					die('Wrong variable for action!');
			}
		}
		else {
			$this->dialogChangePasswordShow();
		}
	}

	/** Checks if the Module gets called after the user has changed the Password
	 * Its function is to disallow access to this module at a later time, with the
	 * links linking to this module hacked into the browser
	 */
	private function firstLoginCheck() {
		$user = $this->_userManager->getUserData($_SESSION ['uid']);
		if(!$user ['first_passwd']) {
			$this->_interface->DieError('Kein Zugriff auf dieses Modul; Dass Passwort wurde schon einmal geändert');
		}
	}

	private function dialogChangePasswordShow() {
		$onFirstLoginChangeEmail = $this->emailChangeOnFirstLoginGet();
		$emailChangeForced = $this->emailChangeOnFirstLoginForcedGet();
		$userEmail = $this->userEmailGet();
		$this->_smarty->assign('onFirstLoginChangeEmail', $onFirstLoginChangeEmail);
		$this->_smarty->assign('emailChangeForced', $emailChangeForced);
		$this->_smarty->assign('userEmail', $userEmail);
		$this->_smarty->display($this->_smartyPath . 'changePasswordDialog.tpl');
	}

	private function changePasswordHandle() {
		$pw = $_POST ['newPassword'];
		$repeatPw = $_POST ['newPasswordRepeat'];
		$this->changePasswordCheckInput($pw, $repeatPw);
		$this->changePasswordToDatabase($pw);
	}

	private function changePasswordCheckInput($pw, $repeatPw) {
		if($pw !== $repeatPw) {
			$this->_interface->DieError('Das eingegebene Passwort stimmt nicht mit der Wiederholung überein!');
		}
		try {
			inputcheck($pw, 'password');
		} catch(WrongInputException $e) {
			$this->_interface->DieError('Das Passwort enthält nicht unterstützte Zeichen oder ist zu kurz / zu lang!');
		}
		try {
			$presetPw = $this->_globalSettingsManager->valueGet(GlobalSettings::PRESET_PASSWORD);
		} catch(MySQLVoidDataException $e) {
			$this->_interface->DieError('Konnte das voreingestellte Passwort nicht abrufen');
		}
		if($presetPw == hash_password($pw)) {
			$this->_interface->DieError('Dass Passwort ist das gleiche wie das voreingestellte! Nimm ein anderes!');
		}
	}

	private function changePasswordToDatabase($pw) {
		try {
			$this->_userManager->changePassword($_SESSION['uid'] ,$pw);
		} catch(Exception $e) {
			$this->_interface->DieError('Konnte das Passwort nicht verändern!');
		}
	}

	private function isFirstPasswordOfUserSet($value) {
		try {
			$this->_userManager->setFirstPasswordOfUser($_SESSION ['uid'], $value);
		} catch(Exception $e) {
			$this->_interface->DieError('Konnte die Passwortänderung nicht speichern! Bitte kontaktiere den Administrator!');
		}
	}

	private function emailChangeOnFirstLoginHandle() {
		if($this->emailChangeOnFirstLoginGet()) {
			$newEmail = $_POST ['newEmail'];
			if($newEmail == '' && !$this->emailChangeOnFirstLoginForcedGet()) {
				//Email not forced
			}
			else {
				$this->emailChangeOnFirstLoginCheckInput($newEmail);
				$this->emailSet($newEmail);
			}
		}
	}

	private function emailChangeOnFirstLoginGet() {
		try {
			$email = $this->_globalSettingsManager->valueGet(GlobalSettings::FIRST_LOGIN_CHANGE_EMAIL);
		} catch(MySQLVoidDataException $e) {
			$this->_interface->DieError('Datenbankfehler: Konnte nicht herausfinden ob die Email für einen ErstLogin benötigt wird; Installation fehlerhaft / nicht vollständig');
		} catch(Exception $e) {
			$this->_interface->DieError('Datenbankfehler: Konnte nicht herausfinden ob die Email für einen ErstLogin benötigt wird');
		}
		return($email != '0');
	}

	private function emailChangeOnFirstLoginForcedGet() {
		try {
			$email = $this->_globalSettingsManager->valueGet(GlobalSettings::FIRST_LOGIN_CHANGE_EMAIL_FORCED);
		} catch(MySQLVoidDataException $e) {
			$this->_interface->DieError('Datenbankfehler: Konnte nicht herausfinden ob die Email für einen ErstLogin benötigt wird; Installation fehlerhaft / nicht vollständig');
		} catch(Exception $e) {
			$this->_interface->DieError('Datenbankfehler: Konnte nicht herausfinden ob die Email für einen ErstLogin benötigt wird');
		}
		return($email != '0');
	}

	private function userEmailGet() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT email FROM SystemUsers WHERE ID = :userId'
			);
			$stmt->execute(array('userId' => $_SESSION['uid']));
			return $stmt->fetchColumn();

		} catch (\Exception $e) {
			$this->_logger->log('Error fetching email for user', 'Notice',
				Null, json_encode(array('msg' => $e->getMessage())));
			return '';
		}
	}

	private function emailSet($email) {
		try {
			$this->_userManager->changeEmailAdress($_SESSION ['uid'], $email);
		} catch(Exception $e) {
			$this->_interface->DieError('Datenbankfehler: Konnte die Emailadresse nicht verändern');
		}
	}

	private function emailChangeOnFirstLoginCheckInput($email) {
		try {
			inputcheck($email, 'email', 'Email');
		} catch(WrongInputException $e) {
			$this->_interface->DieError('Die Email-Adresse wurde falsch eingegeben; Bitte gebe eine gültige EmailAdresse ein. <br>(Hinweis: Die Emailadresse wird dafür benötigt, um dich zu erreichen, wenn etwas schief läuft; Es ist wichtig das du darüber erreichbar bist)');
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	protected $_smarty;
	protected $_smartyPath;
	protected $_interface;
	protected $_userManager;
	protected $_globalSettingsManager;
}

?>
