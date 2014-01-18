<?php

namespace administrator\System\User\UserUpdateWithSchoolyearChange;

require_once 'UserUpdateWithSchoolyearChange.php';

/**
 * Allows the user to resolve the conflicts
 **/
class ConflictsResolve extends \administrator\System\User\UserUpdateWithSchoolyearChange {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->conflictResolveFormDisplay();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
	}

	/**
	 * Fetches 20 conflicts that are to resolve
	 * Dies displaying a message on error
	 * @return array  the conflicts
	 */
	protected function conflictsToResolveGet() {

		try {
			$res = $this->_pdo->query(
				'SELECT tc.ID as conflictId, tc.tempUserId AS userId,
					tc.type AS type,
					IFNULL(u.forename, tu.forename) AS forename,
					IFNULL(u.name, tu.name) AS name,
					CONCAT(g.gradelevel, "-", g.label) AS origGrade,
					CONCAT(tu.gradelevel, "-", tu.label) AS newGrade
				FROM UserUpdateTempConflicts tc
					LEFT JOIN UserUpdateTempUsers tu ON tu.ID = tc.tempUserId
					LEFT JOIN users u ON u.ID = tc.origUserId
					LEFT JOIN usersInGradesAndSchoolyears uigs
						ON u.ID = uigs.userId
						AND uigs.schoolyearId = @activeSchoolyear
					LEFT JOIN Grades g ON uigs.gradeId = g.ID
				WHERE solved = 0
					ORDER BY type LIMIT 10'
			);
			return $res->fetchAll(\PDO::FETCH_ASSOC);

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching conflicts to resolve',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the data!'));
		}
	}

	protected function conflictResolveFormDisplay() {

		$this->_smarty->assign('conflicts', $this->conflictsToResolveGet());
		$this->displayTpl('conflictResolve.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>