<?php

namespace administrator\System\User\UserUpdateWithSchoolyearChange;

require_once 'UserUpdateWithSchoolyearChange.php';

class SessionMenu extends \administrator\System\User\UserUpdateWithSchoolyearChange {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->menuDisplay();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
	}

	protected function menuDisplay() {

		$values = $this->conflictCountGet();
		$this->_smarty->assign('openConflictsCount', $values['openConflicts']);
		$this->_smarty->assign(
			'solvedConflictsCount',$values['solvedConflicts']
		);
		$this->displayTpl('menu.tpl');
	}

	protected function conflictCountGet() {

		try {
			$res = $this->_pdo->query(
				'SELECT (
					SELECT COUNT(*) FROM UserUpdateTempConflicts
					WHERE solved = 0
				) AS openConflicts,
				(
					SELECT COUNT(*) FROM UserUpdateTempConflicts
					WHERE solved = 1
				) AS solvedConflicts
			');

			return $res->fetch();

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching conflictcount', 'Notice',
				Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the data!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>