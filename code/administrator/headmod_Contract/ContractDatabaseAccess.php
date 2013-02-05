<?php

/**
 * This class acts as the central point for accessing data of the database. Additionally, it does most of the
 * Exceptionhandling.
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class NachrichtenDatabaseAccess {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface) {

		require_once 'NachrichtenLanguageManager.php';

		$this->_interface = $interface;
		$this->_languageManager = new NachrichtenLanguageManager($this->_interface);
		$this->_languageManager->setModule('NachrichtenDatabaseAccess');
		$this->initManagers();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function initManagers () {


		require_once PATH_ACCESS_CONTRACT . '/NachrichtenUsersManager.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';

		$this->_userManager = new NachrichtenUsersManager();
		$this->_globalSettingsManager = new GlobalSettingsManager();
		
	}

	
	public function classRegistrationGloballyIsEnabledSet ($toggle) {

		try {
			$this->_globalSettingsManager->valueSet (GlobalSettings::IS_CLASSREGISTRATION_ENABLED, $toggle);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('globalSettingsErrorSetClassRegEnabled'));
		}
	}


	public function userAdd ($forename, $name, $username, $password, $email, $telephone, $birthday) {

		try {
			$this->_userManager->addUser($forename, $name, $username, $password, $email, $telephone, $birthday);
		} catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('userErrorAdd'), $e->getMessage()));
		}
	}

	public function userDelete ($userId) {

		try {
			$this->_userManager->deleteUser($userId);
		} catch (Exception $e) {
			$this->_interface->dieError(sprintf($this->_languageManager->getText('userErrorDelete'), $e->getMessage()));
		}
	}

	public function userGetAll () {
		try {
			$users = $this->_userManager->getAllUsers();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorNoUsers'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorFetch'));
		}
		return $users;
	}

	public function userChangePasswordByUserIdArray ($password) {
		try {
			$this->_userManager->changePasswordOfUserIdArray ($password);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError ($this->_languageManager->getText ('userErrorChangePasswords'));
		} catch (Exception $e) {
			$this->_interface->dieError ($this->_languageManager->getText ('userErrorChangePasswords'));
		}
	}

	public function userIdAddToUserIdArray ($userId) {
		$this->_userManager->addUserIdToUserIdArray($userId);
	}

	public function userGetByUserIdArray () {
		try {
			$users = $this->_userManager->getUsersByUserIdArray ();
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorFetch'));
		}
		return $users;
	}

	public function userGet ($userId) {

		try {
			$userData = $this->_userManager->getUserByID($userId);
		} catch (MySQLConnectionException $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorNoSpecific'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('userErrorFetch'));
		}
		return $userData;
	}


	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_interface;
	private $_languageManager;

	/********************
	 * Managers
	********************/
	private $_userManager;
	private $_globalSettingsManager;
	
}
?>