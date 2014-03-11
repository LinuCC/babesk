<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ACCESS . '/GlobalSettingsManager.php';
require_once PATH_WEB . '/headmod_Kuwasys/Kuwasys.php';

class ClassDetails extends Kuwasys {

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	///////////////////////////////////////////////////////////////////////
	//Getters and Setters
	///////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
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

	///////////////////////////////////////////////////////////////////////
	//Implementations
	///////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->initSmartyVariables();
		$this->_globalSettingsManager = new GlobalSettingsManager();
		$this->_smarty->assign(
			'inh_path', PATH_SMARTY . '/templates/web/baseLayout.tpl'
		);
		$this->_interface->addButton(
			_g('Go to Main menu'),
			'index.php?module=web|Kuwasys'
		);
	}

	/**
	 * Fetches and returns the data of the class by the given Id
	 * @param  int    $classId The id of the class
	 * @return array           The data of the class
	 */
	private function classDetailsGet($classId) {

		try {
			$stmt = $this->_pdo->prepare('SELECT * FROM KuwasysClasses WHERE ID = ?');
			$stmt->execute(array($classId));
			return $stmt->fetch(\PDO::FETCH_ASSOC);

		} catch(\PDOException $e) {
			$this->_logger->log('Error fetching the class by id',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->DieError(_g('Could not fetch the class!'));
		}
	}

	/**
	 * Checks if class-registration is enabled or not
	 * @return bool   true if it is enabled, else false
	 */
	private function getIsClassRegistrationGloballyEnabled() {

		try {
			$value = $this->_globalSettingsManager->valueGet(GlobalSettings::IS_CLASSREGISTRATION_ENABLED);
		} catch(Exception $e) {
			$this->_interface->DieError('Ein Fehler ist beim Abrufen vom KurswahlWert aufgetreten. Breche ab.');
		}
		return $value == 1;
	}

	/**
	 * Deletes a link between a user and a class
	 * @param  int    $userId  The id of the user
	 * @param  int    $classId The id of the class
	 */
	private function deleteJointUsersInClass($userId, $classId) {

		try {
			$stmt = $this->_pdo->prepare(
				'DELETE FROM KuwasysUsersInClasses
					WHERE UserID = ? AND ClassID = ?'
			);
			$stmt->execute(array($userId, $classId));

		} catch(\PDOException $e) {
			$this->_logger->log('error deleting userInClass-Connection',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->DieError(
				'Could not remove you from the class!'
			);
		}
	}

	/**
	 * Displays the details of the class to the user
	 */
	private function showClassDetails() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT c.ID, c.label, c.description,
					uics.translatedName AS status, c.registrationEnabled
					FROM KuwasysClasses c
						INNER JOIN KuwasysUsersInClasses uic
							ON uic.ClassID = c.ID
						INNER JOIN KuwasysUsersInClassStatuses uics
							ON uics.Id = uic.statusId
					WHERE uic.userId = ? AND uic.classId = ?
						AND c.schoolyearId = @activeSchoolyear
				'
			);
			$stmt->execute(array($_SESSION['uid'], $_GET['classId']));
			$data = $stmt->fetch(\PDO::FETCH_ASSOC);
			$this->_smarty->assign('class', $data);
			$this->displayTpl('classDetails.tpl');

		} catch(\PDOException $e) {
			$this->_logger->log('Error fetching the class-details',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g(
				'Could not display the class-details!')
			);
		}
	}

	/**
	 * Displays a confirmation whether the user really wants to deregister
	 */
	private function showConfirmationDeRegisterClass() {

		$classId = $_GET['classId'];
		$class = $this->classDetailsGet($classId);
		$this->_smarty->assign('class', $class);
		$this->_smarty->display(
			$this->_smartyPath . 'deRegisterClassConfirmation.tpl'
		);
	}

	/**
	 * deregisters the user from the class
	 */
	private function deRegisterUserFromClass() {

		$class = $this->classDetailsGet($_GET['classId']);
		$this->deRegisterAllowedCheck($class);
		$this->deleteJointUsersInClass($_SESSION['uid'], $class['ID']);
		$this->_interface->DieMessage(sprintf('Sie wurden erfolgreich vom Kurs %s abgemeldet. %s', $class ['label'], Kuwasys::$buttonBackToMM));
	}

	/**
	 * Checks if the deregistering of the user is allowed
	 * Dies displaying a message if it is not allowed
	 * @param  array  $class The data of the class to deregister from
	 * @return bool          true if it is allowed
	 */
	private function deRegisterAllowedCheck($class) {

		if(!$class['registrationEnabled']) {
			$this->_interface->dieError('Dieser Kurs erlaubt momentan keine Abmeldungen!');
		}
		else if(!$this->getIsClassRegistrationGloballyEnabled()) {
			$this->_interface->dieError('Kursan- und abmeldungen sind momentan gesperrt!');
		}
		else if(!isset($_POST['yes'])) {
			$this->_interface->DieMessage(sprintf('Sie wurden nicht vom Kurs %s abgemeldet', $class ['label']));
		}
		else {
			return true;
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	protected $_jointUsersInClass;
	protected $_globalSettingsManager;
	protected $_smartyPath;
}

?>
