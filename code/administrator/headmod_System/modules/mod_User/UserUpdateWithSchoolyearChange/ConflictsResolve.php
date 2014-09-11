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
		if(isset($_POST['change'])) {
			$this->conflictsResolveByInput();
		}
		else if(isset($_POST['cancel'])) {
			//Now execute the SessionMenu-Module
			$mod = new \ModuleExecutionCommand('root/administrator/System/' .
				'User/UserUpdateWithSchoolyearChange/SessionMenu');
			$this->_dataContainer->getAcl()->moduleExecute(
				$mod, $this->_dataContainer
			);
		}
		else {
			$this->conflictResolveFormDisplay();
		}
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
	private function conflictsToResolveGet() {

		try {
			$res = $this->_pdo->query(
				'SELECT tc.ID as conflictId, tc.tempUserId AS userId,
					tc.type AS type,
					IFNULL(u.forename, tu.forename) AS forename,
					IFNULL(u.name, tu.name) AS name,
					IFNULL(tu.birthday, u.birthday) AS birthday,
					CONCAT(g.gradelevel, "-", g.label) AS origGrade,
					CONCAT(tu.gradelevel, "-", tu.label) AS newGrade
				FROM UserUpdateTempConflicts tc
					LEFT JOIN UserUpdateTempUsers tu ON tu.ID = tc.tempUserId
					LEFT JOIN SystemUsers u ON u.ID = tc.origUserId
					LEFT JOIN SystemUsersInGradesAndSchoolyears uigs
						ON u.ID = uigs.userId
						AND uigs.schoolyearId = @activeSchoolyear
					LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
				WHERE solved = 0
					ORDER BY type LIMIT 10'
			);
			$data = $res->fetchAll(\PDO::FETCH_ASSOC);
			return $data;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching conflicts to resolve',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the data!'));
		}
	}

	private function conflictResolveFormDisplay() {

		$this->_smarty->assign('conflicts', $this->conflictsToResolveGet());
		$this->displayTpl('conflictResolve.tpl');
	}

	/**
	 * Parses the input of the user to resolve the conflicts
	 */
	private function conflictsResolveByInput() {

		if(empty($_POST['conflict'])) {
			$this->_interface->backlink('administrator|System|User|UserUpdateWithSchoolyearChange|SessionMenu|ConflictsResolve');
			$this->_interface->dieError(_g('Please answer the questions given to resolve the conflicts.'));
		}

		$conflicts = $this->conflictDataAddToIdArray($_POST['conflict']);

		$this->resolveSqlStatementsPrepare();

		foreach($conflicts as $conflict) {
			$this->resolveByConflictType($conflict);
		}

		$this->conflictResolveFormDisplay();
	}

	/**
	 * Fetches the types of conflicts for the given ids
	 * Dies displaying a message on error
	 * @param  array  $ids '<id>' => [...]
	 * @return array       the conflicts '<id>' => ['type' => '<type>', ...]
	 */
	private function conflictTypesAddToByIdArray(array $ids) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT type FROM UserUpdateTempConflicts WHERE ID = ?'
			);
			foreach($ids as $id => $stuff) {
				$stmt->execute(array($id));
				$res = $stmt->fetchColumn();
				$ids[$id]['type'] = $res;
			}
			$stmt->closeCursor();
			return $ids;

		} catch (\PDOException $e) {
			$this->_logger->log('Could not fetch the conflict types by array',
			'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Error fetching the data!'));
		}
	}

	/**
	 * Sets prepared Statements so that they can be used later on
	 * Dies displaying a message on error
	 */
	private function resolveSqlStatementsPrepare() {

		try {
			$this->userSolveStmt = $this->_pdo->prepare(
				'INSERT INTO UserUpdateTempSolvedUsers
					(origUserId, forename, name, newUsername, newTelephone,
						newEmail, gradelevel, gradelabel, birthday)
					VALUES (
						:origUserId, :forename, :name, :newUsername,
						:newTelephone, :newEmail, :gradelevel, :gradelabel,
						:birthday
					)'
			);
			$this->conflictResolveStmt = $this->_pdo->prepare(
				'UPDATE UserUpdateTempConflicts SET solved = 1 WHERE ID = :id'
			);

		} catch (\PDOException $e) {
			$this->_logger->log('Could not set the prepared statements',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Checks what method needs to be executed for the conflict-type
	 * Dies displaying a message on wrong type
	 * @param  array  $conflict An array containing the conflict-data
	 */
	private function resolveByConflictType($conflict) {

		try {
			switch($conflict['type']) {
				case 'CsvOnlyConflict':
					$this->csvOnlyResolve($conflict);
					break;
				case 'DbOnlyConflict':
					$this->dbOnlyResolve($conflict);
					break;
				case 'GradelevelConflict':
					$this->gradelevelResolve($conflict);
					break;
				default:
					$this->_interface->dieError(_g('Wrong type?!'));
			}

		} catch (\PDOException $e) {
			$this->_logger->log('Error uploading the conflict resolve',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Adds the data of the conflicts to the given id-Array
	 * @param  array  $ids '<conflictId>' => ['status' => '<status>']
	 * @return array       '<conflictId>' => [
	 *                         'status' => '<status>',
	 *                         'origUserId' => '<original UserId'>,
	 *                         ...
	 *                     ]
	 */
	private function conflictDataAddToIdArray(array $ids) {

		$query = 'SELECT tc.ID as conflictId, tc.tempUserId AS tempUserId,
					tc.origUserId AS origUserId,
					tc.tempUserId AS tempUserId,
					IFNULL(tu.birthday, u.birthday) AS birthday,
					tu.newUsername AS newUsername,
					tu.newTelephone AS newTelephone,
					tu.newEmail AS newEmail,
					tc.type AS type,
					IFNULL(u.forename, tu.forename) AS forename,
					IFNULL(u.name, tu.name) AS name,
					g.gradelevel AS origGradelevel,
					g.label AS origGradelabel,
					tu.gradelevel AS newGradelevel,
					tu.label AS newGradelabel
				FROM UserUpdateTempConflicts tc
				LEFT JOIN UserUpdateTempUsers tu ON tu.ID = tc.tempUserId
				LEFT JOIN SystemUsers u ON u.ID = tc.origUserId
				LEFT JOIN SystemUsersInGradesAndSchoolyears uigs
					ON u.ID = uigs.userId
					AND uigs.schoolyearId = @activeSchoolyear
				LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
				WHERE tc.ID = ?';

		try {
			$stmt = $this->_pdo->prepare($query);

			foreach($ids as $id => $stuff) {
				$stmt->execute(array($id));
				$res = $stmt->fetch(\PDO::FETCH_ASSOC);
				$ids[$id] = array_merge($ids[$id], $res);
			}

			$stmt->closeCursor();
			return $ids;

		} catch (\PDOException $e) {
			$this->_logger->log('Could not fetch the conflict-data by array',
			'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Error fetching the data!'));
		}
	}

	private function dbOnlyResolve($conflict) {

		if($conflict['status'] == 'confirmed') {
			//User is not in the new schoolyear
			$this->conflictResolveStmt->execute(
				array(':id' => $conflict['conflictId'])
			);
		}
		else if($conflict['status'] == 'correctedUsername') {
			$this->_interface->dieError(_g('Not implemented yet!'));
		}
	}

	private function csvOnlyResolve($conflict) {

		$data = array(
			'origUserId' => 0,
			'forename' => $conflict['forename'],
			'name' => $conflict['name'],
			'newUsername' => $conflict['newUsername'],
			'newTelephone' => $conflict['newTelephone'],
			'newEmail' => $conflict['newEmail'],
			'gradelevel' => $conflict['newGradelevel'],
			'gradelabel' => $conflict['newGradelabel'],
			'birthday' => $conflict['birthday'],
		);
		if(empty($conflict['birthday'])) {
			$conflict['birthday'] = NULL;
		}
		if($conflict['status'] == 'confirmed') {
			$this->userSolveStmt->execute($data);
			$this->conflictResolveStmt->execute(
				array(':id' => $conflict['conflictId'])
			);
		}
		else if($conflict['status'] == 'correctedUserId') {
			$data['origUserId'] = $conflict['correctedUserId'];
			$this->userSolveStmt->execute($data);
			$this->conflictResolveStmt->execute(
				array(':id' => $conflict['conflictId'])
			);
		}
		else if($conflict['status'] == 'correctedUsername') {
			$this->_interface->dieError(_g('Not implemented yet!'));
		}
	}

	private function gradelevelResolve($conflict) {

		if($conflict['status'] == 'confirmed') {
			if(empty($conflict['birthday'])) {
				$conflict['birthday'] = NULL;
			}
			$data = array(
				'origUserId' => $conflict['origUserId'],
				'forename' => $conflict['forename'],
				'name' => $conflict['name'],
				'newUsername' => $conflict['newUsername'],
				'newTelephone' => $conflict['newTelephone'],
				'newEmail' => $conflict['newEmail'],
				'gradelevel' => $conflict['newGradelevel'],
				'gradelabel' => $conflict['newGradelabel'],
				'birthday' => $conflict['birthday']
			);
			$this->userSolveStmt->execute($data);
			$this->conflictResolveStmt->execute(
				array(':id' => $conflict['conflictId'])
			);
		}
		else if($conflict['status'] == 'correctedGrade') {
			$this->_interface->dieError(_g('Not implemented yet!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $userSolveStmt;

	private $conflictResolveStmt;

}

?>