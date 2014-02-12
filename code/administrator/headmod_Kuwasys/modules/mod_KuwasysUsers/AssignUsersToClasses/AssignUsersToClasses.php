<?php

namespace administrator\Kuwasys\KuwasysUsers;

require_once __DIR__ . '/../KuwasysUsers.php';

class AssignUsersToClasses extends \KuwasysUsers {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->mainMenuDisplay();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	private function mainMenuDisplay() {

		$this->_smarty->assign('tableExists', $this->tableExists());
		$this->displayTpl('mainmenu.tpl');
	}

	/**
	 * Checks if the Table for the Temporary assignUsersToClasses data exists
	 *
	 * Dies displaying a Message when the Query could not be executed
	 *
	 * @return boolean true if it exists, else false
	 */
	protected function tableExists() {

		try {
			$stmt = $this->_pdo->query(
				'SHOW TABLES LIKE "KuwasysTemporaryRequestsAssign";');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not check if the UsersToClasses-Table exists!') .
				$e->getMessage());
		}

		return (boolean) $stmt->fetch();
	}

	/**
	 * Fetches the Status with the given Name
	 *
	 * Returns false if Status not found
	 *
	 * @param  string $statusName The Name of the Status to fetch
	 * @return array              The Fetched data of the Status
	 * @throws PDOException If Status could not be fetched
	 */
	protected function statusIdGetByNameWoException($statusName) {

		$stmt = $this->_pdo->prepare('SELECT ID FROM usersInClassStatus
			WHERE name = :name');

		$stmt->execute(array('name' => $statusName));

		return $stmt->fetchColumn();
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>