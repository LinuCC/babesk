<?php

namespace administrator\System\User;

require_once __DIR__ . '/../User.php';
require_once PATH_INCLUDE . '/ModuleExecutionCommand.php';

class UserUpdateWithSchoolyearChange extends \User {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		$this->_smarty->assign('sessionExists', $this->sessionExists());
		$this->displayTpl('start_menu.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Checks if a session already exists
	 * @return bool  true when a session already exists
	 */
	private function sessionExists() {

		try {
			$res = $this->_pdo->query(
				'SHOW TABLES LIKE "UserUpdateTempUsers"'
			);
			return (count($res->fetchAll()) > 0);

		} catch (\PDOException $e) {
			$this->_logger->log('Error checking for already existing Session',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g(
				'Could not check if this has done before!'
			));
		}

	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>