<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersInClassStatusManager.php';
require_once PATH_ACCESS . '/GlobalSettingsManager.php';
require_once PATH_WEB . '/WebInterface.php';
require_once PATH_WEB . '/headmod_Kuwasys/Kuwasys.php';
require_once PATH_WEB . '/headmod_Kuwasys/Kuwasys.php';

class ClassDetails extends Kuwasys {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute ($dataContainer) {

		$this->entryPoint($dataContainer);
		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'deRegisterClassConfirmation':
					$this->showConfirmationDeRegisterClass();
					break;
				case 'deRegisterClass':
					$this->deRegisterUserFromClass();
					break;
			}
		}
		else {
			$this->showClassDetails();
		}
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	protected function entryPoint($dataContainer) {

		$this->_smarty = $dataContainer->getSmarty();
		$this->_interface = new WebInterface($this->_smarty);
		$this->_classManager = new KuwasysClassManager();
		$this->_jointUsersInClass = new KuwasysJointUsersInClass();
		$this->_globalSettingsManager = new GlobalSettingsManager();
		$this->_usersInClassStatusManager = new KuwasysUsersInClassStatusManager ();
	}

	private function getClassByClassId ($classId) {

		try {
			$class = $this->_classManager->getClass($classId);
		} catch (Exception $e) {
			$this->_interface->DieError('Ein Fehler ist beim Abrufen des Kurses aufgetreten.');
		}
		return $class;
	}

	private function getJointUsersInClassByUserIdAndClassId ($classId) {

		try {
			$joint = $this->_jointUsersInClass->getJointOfUserIdAndClassId($_SESSION['uid'], $classId);
		} catch (Exception $e) {
			$this->_interface->DieError('Ein Fehler ist beim Abrufen der Verbindung zu dem Kurs aufgetreten.');
		}
		return $joint;
	}

	private function getIsClassRegistrationGloballyEnabled () {

		try {
			$value = $this->_globalSettingsManager->valueGet (GlobalSettings::IS_CLASSREGISTRATION_ENABLED);
		} catch (Exception $e) {
			$this->_interface->DieError('Ein Fehler ist beim Abrufen vom KurswahlWert aufgetreten. Breche ab.');
		}
		return $value;
	}

	private function deleteJointUsersInClass ($jointId) {

		try {
			$this->_jointUsersInClass->deleteJoint($jointId);
		} catch (Exception $e) {
			$this->_interface->DieError('Konnte die Verbindung zum Kurs nicht löschen!');
		}
	}

	private function showClassDetails () {

		$classId = $_GET['classId'];
		$jointUsersInClass = $this->getJointUsersInClassByUserIdAndClassId($classId);
		$status = $this->statusGetWithoutDieing ($jointUsersInClass['statusId']);
		$this->_smarty->assign('class', $this->getClassByClassId($classId));
		if($status) {
			$this->_smarty->assign('classStatus', $status ['translatedName']);
		}
		$this->_smarty->display($this->_smartyPath . 'classDetails.tpl');
	}

	private function statusGetWithoutDieing ($statusId) {
		try {
			$status = $this->_usersInClassStatusManager->statusGet ($statusId);
		} catch (MySQLVoidDataException $e) {
			return false;
		}
		return $status;
	}

	private function showConfirmationDeRegisterClass () {

		$classId = $_GET['classId'];
		$class = $this->getClassByClassId($classId);
		$this->_smarty->assign('class', $class);
		$this->_smarty->display($this->_smartyPath . 'deRegisterClassConfirmation.tpl');
	}

	private function deRegisterUserFromClass () {

		$class = $this->getClassByClassId($_GET['classId']);
		$joint = $this->getJointUsersInClassByUserIdAndClassId($class ['ID']);
		if(!$class ['registrationEnabled']) {
			$this->_interface->dieError('Dieser Kurs erlaubt momentan keine Abmeldungen!');
		}
		else if (!$this->getIsClassRegistrationGloballyEnabled()) {
			$this->_interface->dieError('Kursan- und abmeldungen sind momentan gesperrt!');
		}
		else if (!isset($_POST['yes'])) {
			$this->_interface->DieMessage(sprintf('Sie wurden nicht vom Kurs %s abgemeldet %s', $class ['label'], Kuwasys::$buttonBackToMM));
		}
		$this->deleteJointUsersInClass($joint ['ID']);
		$this->_interface->DieMessage(sprintf('Sie wurden erfolgreich vom Kurs %s abgemeldet. %s', $class ['label'], Kuwasys::$buttonBackToMM));
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_jointUsersInClass;
	private $_classManager;
	private $_usersManager;
	private $_globalSettingsManager;
	private $_usersInClassStatusManager;
	private $_interface;
	protected $_smarty;
	private $_smartyPath;
}

?>
