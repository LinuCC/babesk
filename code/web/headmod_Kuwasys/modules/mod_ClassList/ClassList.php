<?php

namespace web\Kuwasys;

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Kuwasys/Kuwasys.php';

class ClassList extends \Kuwasys {

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
	 *
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		//Execute submodule "Show" to show the classlist
		$mod = new \ModuleExecutionCommand('root/web/Kuwasys/ClassList/Show');
		$dataContainer->getAcl()->moduleExecute($mod, $dataContainer);
	}

	/**
	 * Checks if the global class-registrations are allowed or not
	 * @return bool   true if they are enabled, false if not
	 */
	protected function globalClassRegistrationsAllowed() {

		try {
			$res = $this->_pdo->query(
				'SELECT value FROM SystemGlobalSettings
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
	//Implements
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>