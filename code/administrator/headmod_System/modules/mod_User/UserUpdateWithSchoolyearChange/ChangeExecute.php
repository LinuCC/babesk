<?php

namespace administrator\System\User\UserUpdateWithSchoolyearChange;

require_once 'UserUpdateWithSchoolyearChange.php';

/**
 * Executes the changes made beforehand.
 */
class ChangeExecute extends \administrator\System\User\UserUpdateWithSchoolyearChange {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if($this->conflictsSolvedCheck()) {
			$this->_pdo->beginTransaction();
			$this->userChangesCommit();
			$this->usersNewCommit();
			$this->schoolyearNewSwitchTo();
			$this->_pdo->commit();
			$this->_interface->backlink('administrator|System|User');
			$this->_interface->dieSuccess(_g(
				'The userdata were changed successfully.'
			));
		}
		else {
			$this->_interface->backlink(
				'administrator|System|User|UserUpdateWithSchoolyearChange' .
				'|SessionMenu'
			);
			$this->_interface->dieError(_g('Please resolve the conflicts ' .
				'before comitting the changes'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Checks if conflicts exist that are not solved yet
	 * @return bool  true if all conflicts are solved
	 */
	private function conflictsSolvedCheck() {

		try {
			$res = $this->_pdo->query(
				'SELECT COUNT(*) FROM UserUpdateTempConflicts WHERE solved = 0
			');
			return $res->fetchColumn() == 0;

		} catch (\PDOException $e) {
			$this->_logger->log('Error checking for not solved conflicts',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/**
	 * Commits the changes to the users to the real users-table
	 * Dies displaying a message on error
	 */
	private function userChangesCommit() {

		try {
			$query = 'INSERT INTO usersInGradesAndSchoolyears (
					userId, gradeId, schoolyearId
				) SELECT su.origUserId, g.ID,
					(SELECT value FROM global_settings
						WHERE name =
							"userUpdateWithSchoolyearChangeNewSchoolyearId"
					) AS schoolyear
				FROM UserUpdateTempSolvedUsers su
					LEFT JOIN Grades g ON g.gradelevel = su.gradelevel AND
						g.label = su.gradelabel
					WHERE su.origUserId <> 0
				';

			$this->_pdo->query($query);

		} catch (\PDOException $e) {
			$this->_logger->log('Could not commit the user Changes',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g(
				'Could not upload the userchanges!')
			);
		}
	}

	/**
	 * Adds the users that are new to the database (were in csv but not in db)
	 * Dies displaying a message on error.
	 */
	private function usersNewCommit() {

		try {
			$users = $this->usersNewToCommitFetch();
			if(empty($users) || !count($users)) {
				return;
			}

			$stmtu = $this->_pdo->prepare(
				'INSERT INTO users
					(forename, name, birthday)
				VALUES (?, ?, ?)'
			);
			$stmtg = $this->_pdo->prepare(
				'INSERT INTO usersInGradesAndSchoolyears (
					userId, gradeId, schoolyearId
				) VALUES (? ,?, (SELECT value FROM global_settings
					WHERE name =
						"userUpdateWithSchoolyearChangeNewSchoolyearId"
				))'
			);

			foreach($users as $user) {
				if(empty($user['birthday'])) {
					$user['birthday'] = '';
				}
				if(empty($user['gradeId'])) {
					$user['gradeId'] = $this->gradeAdd(
						$user['gradelevel'], $user['gradelabel']
					);
				}
				$stmtu->execute(
					array($user['forename'], $user['name'], $user['birthday'])
				);
				$userId = $this->_pdo->lastInsertId();
				$stmtg->execute(array($userId, $user['gradeId']));
			}

		} catch (\PDOException $e) {
			$this->_logger->log('Could not commit the new users',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not commit the new users!' . $e->getMessage()));
		}
	}

	/**
	 * Adds a grade and returns its new Id
	 * Dies displaying a message on error
	 * @param  int    $level The gradelevel
	 * @param  string $label The gradelabel
	 * @return int           The Id of the new grade
	 */
	private function gradeAdd($level, $label) {

		try {
			if(empty($this->_gradeStmt)) {
				$this->_gradeStmt = $this->_pdo->prepare(
					'INSERT INTO Grades (label, gradelevel) VALUES (?,?)'
				);
			}
			$this->_gradeStmt->execute(array($level, $label));
			return $this->_pdo->lastInsertId();

		} catch (\PDOException $e) {
			$this->_logger->log('Error adding the grade', 'Notice', Null,
				json_encode(array(
					'msg' => $e->getMessage(),
					'level' => $level,
					'label' => $label))
			);
			$this->_interface->dieError(_g('Could not add the grade!'));
		}
	}

	/**
	 * Fetches the users that will be added when comitting
	 * @return array  the users to add
	 */
	private function usersNewToCommitFetch() {

		try {
			$res = $this->_pdo->query('SELECT su.*, g.ID AS gradeId
				FROM UserUpdateTempSolvedUsers su
				LEFT JOIN Grades g ON su.gradelevel = g.gradelevel AND
					su.gradelabel = g.label
				WHERE su.origUserId = 0');
			$users = $res->fetchAll(\PDO::FETCH_ASSOC);

			return $users;

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the new users to commit',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the new users!'));
		}
	}

	/**
	 * Switches to the new schoolyear
	 * Dies displaying a message on error
	 */
	private function schoolyearNewSwitchTo() {

		try {
			$this->_pdo->query(
				'UPDATE schoolYear SET active = 0 WHERE active = 1;
				UPDATE schoolYear SET active = 1 WHERE ID = (
					SELECT value FROM global_settings
						WHERE name =
							"userUpdateWithSchoolyearChangeNewSchoolyearId")'
			);

		} catch (\PDOException $e) {
			$this->_logger->log('Could not switch to the new Schoolyear!',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not switch to the new Schoolyear!'));
		}
	}



	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Use a prepared statement, so if multiple grades are added, its faster
	 * @var PDOStatement
	 */
	private $_gradeStmt;
}

?>