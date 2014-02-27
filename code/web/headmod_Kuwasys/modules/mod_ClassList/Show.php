<?php

namespace web\Kuwasys\ClassList;

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Kuwasys/modules/mod_ClassList/ClassList.php';

class Show extends \web\Kuwasys\ClassList {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Module
	 * @param string $name         The Name of the Module
	 * @param string $display_name The Name that should be displayed to the
	 *                             User
	 * @param string $path         A relative Path to the Module
	 */
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the Module, does things based on ExecutionRequest
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		try {
			$regEnabled = $this->globalClassRegistrationsAllowed();
			if($regEnabled) {
				$classes = $this->classesFetch();
				$units = $this->unitsFetch();
				$this->_smarty->assign('classUnits', $units);
				$this->_smarty->assign('classes', $classes);
				$this->displayTpl('classList.tpl');
			}
			else {
				$this->_interface->dieError(
					_g('Registrations are not allowed for the moment!')
				);
			}
		} catch (Exception $e) {
			$this->_interface->dieError(_g('Could not display the list!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Initializes data needed by the Object
	 * @param  DataContainer $dataContainer Contains data needed by Classes
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->initSmartyVariables();
	}

	/**
	 * Fetches the data necessary to display the class-list
	 * @return array  the fetched data
	 */
	protected function classesFetch() {

		try {
			$res = $this->_pdo->query(
				'SELECT c.*, GROUP_CONCAT(
						DISTINCT ct.forename, " ", ct.name SEPARATOR ", "
					) AS classteacher
					FROM class c
					LEFT JOIN jointClassTeacherInClass ctic
						ON ctic.ClassID = c.ID
					LEFT JOIN classTeacher ct ON ct.ID = ctic.ClassTeacherID
					WHERE c.schoolyearId = @activeSchoolyear
					GROUP BY c.ID'
			);
			$data = $res->fetchAll(\PDO::FETCH_ASSOC);
			return $data;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the data to display',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	/**
	 * Fetches the class-units from the database
	 * @return array  The class-units
	 */
	protected function unitsFetch() {

		try {
			$res = $this->_pdo->query(
				'SELECT * FROM kuwasysClassUnit ORDER BY ID'
			);
			$data = $res->fetchAll(\PDO::FETCH_ASSOC);
			return $data;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the units to display',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	/**
	 * Checks if the global class-registrations are allowed or not
	 * @return bool   true if they are enabled, false if not
	 */
	protected function globalClassRegistrationsAllowed() {

		try {
			$res = $this->_pdo->query(
				'SELECT value FROM global_settings
					WHERE name = "isClassRegistrationEnabled"'
			);
			$value = $res->fetchColumn();
			return $value != 0;

		} catch (\PDOException $e) {
			$this->_logger->log(
				'Error checking if globalClassRegistrations are enabled',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>