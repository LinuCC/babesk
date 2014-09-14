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
		$this->_smartyPath = PATH_SMARTY_TPL . '/web' . $path;
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
			'inh_path', PATH_SMARTY_TPL . '/web/baseLayout.tpl'
		);
		$this->_interface->addButton(
			_g('Go to Main menu'),
			'index.php?module=web|Kuwasys'
		);
	}

	/**
	 * Checks if class-registration is enabled or not
	 * @return bool   true if it is enabled, else false
	 */
	private function getIsClassRegistrationGloballyEnabled() {

		try {
			$value = $this->_globalSettingsManager->valueGet(GlobalSettings::IS_CLASSREGISTRATION_ENABLED);
		} catch(Exception $e) {
			$this->_interface->dieError('Ein Fehler ist beim Abrufen vom KurswahlWert aufgetreten. Breche ab.');
		}
		return $value == 1;
	}

	/**
	 * Deletes a link between a user and a class
	 * @param  int    $userId  The id of the user
	 * @param  int    $classId The id of the class
	 */
	private function deleteJointUsersInClass($userId, $classId, $categoryId) {

		try {
			$stmt = $this->_pdo->prepare(
				'DELETE FROM KuwasysUsersInClassesAndCategories
					WHERE UserID = ? AND ClassID = ? AND categoryId = ?'
			);
			$stmt->execute(array($userId, $classId, $categoryId));

		} catch(\PDOException $e) {
			$this->_logger->log('error deleting userInClass-Connection',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->setBacklink('index.php?module=web|Kuwasys');
			$this->_interface->dieError(
				'Could not remove you from the class!'
			);
		}
	}

	/**
	 * Fetches the data of the class chosen by the user
	 * @param  int    $classId The id of the class from which to fetch the
	 *                         details
	 * @return array           The classdata
	 */
	private function detailsOfChosenClassGet($classId, $categoryId) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT c.*, uics.name, uics.translatedName AS status,
					GROUP_CONCAT(
						DISTINCT CONCAT(ct.forename, " ", ct.name)
						SEPARATOR ", "
					) AS classteacherName, uicc.categoryId AS categoryId
					FROM KuwasysClasses c
						INNER JOIN KuwasysUsersInClassesAndCategories uicc
							ON uicc.ClassID = c.ID AND uicc.categoryId = ?
						INNER JOIN KuwasysUsersInClassStatuses uics
							ON uics.Id = uicc.statusId
						LEFT JOIN KuwasysClassteachersInClasses ctic
							ON ctic.ClassID = c.ID
						LEFT JOIN KuwasysClassteachers ct
							ON ct.ID = ctic.ClassTeacherID
					WHERE uicc.userId = ? AND uicc.classId = ?
						AND c.schoolyearId = @activeSchoolyear
					GROUP BY c.ID
				'
			);
			$stmt->execute(array($categoryId, $_SESSION['uid'], $classId));
			$data = $stmt->fetch(\PDO::FETCH_ASSOC);
			return $data;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the class-details',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g(
				'Could not fetch the class-details!')
			);
		}
	}

	/**
	 * Displays the details of the class to the user
	 */
	private function showClassDetails() {

		try {
			$data = $this->detailsOfChosenClassGet(
				$_GET['classId'], $_GET['categoryId']
			);
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

		$class = $this->detailsOfChosenClassGet(
			$_GET['classId'], $_GET['categoryId']
		);
		$this->_smarty->assign('class', $class);
		$this->_smarty->display(
			$this->_smartyPath . 'deRegisterClassConfirmation.tpl'
		);
	}

	/**
	 * deregisters the user from the class
	 */
	private function deRegisterUserFromClass() {

		$class = $this->detailsOfChosenClassGet(
			$_GET['classId'], $_GET['categoryId']
		);
		$this->deRegisterAllowedCheck($class);
		$this->deleteJointUsersInClass(
			$_SESSION['uid'], $class['ID'], $class['categoryId']
		);
		$this->_interface->setBacklink('index.php?module=web|Kuwasys');
		$this->_interface->dieSuccess(sprintf('Sie wurden erfolgreich vom Kurs %s abgemeldet.', $class ['label']));
	}

	/**
	 * Checks if the deregistering of the user is allowed
	 * Dies displaying a message if it is not allowed
	 * @param  array  $class The data of the class to deregister from
	 * @return bool          true if it is allowed
	 */
	private function deRegisterAllowedCheck($class) {

		if(!$class['registrationEnabled']) {
			$this->_interface->setBacklink('index.php?module=web|Kuwasys');
			$this->_interface->dieError('Dieser Kurs erlaubt momentan keine Abmeldungen!');
		}
		else if(!$this->getIsClassRegistrationGloballyEnabled()) {
			$this->_interface->setBacklink('index.php?module=web|Kuwasys');
			$this->_interface->dieError('Kursan- und abmeldungen sind momentan gesperrt!');
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
