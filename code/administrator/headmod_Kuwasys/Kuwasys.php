<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_INCLUDE . '/exception_def.php';
require_once 'KuwasysDataContainer.php';
require_once 'KuwasysLanguageManager.php';

class Kuwasys extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct ($name, $display_name,$headmod_menu) {

		parent::__construct($name, $display_name,$headmod_menu);
		defined('PATH_ACCESS_KUWASYS') or define('PATH_ACCESS_KUWASYS', PATH_ACCESS . '/headmod_Kuwasys');
		defined('PATH_INCLUDE_KUWASYS') or define('PATH_INCLUDE_KUWASYS', PATH_INCLUDE . '/headmod_Kuwasys');
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute ($dataContainer) {
		//function not needed, javascript is doing everything
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**===============================**
	 * Functions usable by Submodules *
	 **===============================**/

	/**
	 * Fetches a Class from the Database
	 *
	 * @param  int    $classId The ID of the Class to fetch
	 * @return array           The Class-Data
	 */
	protected function classGet($classId) {

		try {
			$stmt = $this->_pdo->prepare('SELECT * FROM class
				WHERE ID = :classId');

			$stmt->execute(array('classId' => $classId));
			return $stmt->fetch();

		} catch (PDOException $e) {
			$msg = "Could not fetch the Class with Id $classId.";
			$this->_logger->log(__METHOD__ . ": $msg", 'Moderate', NULL,
				json_encode(array('error' => $e->getMessage())));
			throw new PDOException($msg, 0, $e);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}
?>
