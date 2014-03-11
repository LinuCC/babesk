<?php

/**
 * Handles the Switch from one Schoolyear to another with many datachanges
 */
class SchoolyearSwitch {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($interface) {

		$this->_interface = $interface;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($newSchoolyearId) {

		$this->_schoolyearId = $newSchoolyearId;
		try {
			$this->settingsInputHandle();
			$this->uploadStart();
			$this->upload();
			$this->uploadFinish();
			$this->_interface->dieSuccess(
				_g('Successfully switched the Schoolyear!'));

		} catch (Exception $e) {

			$this->_interface->dieError(
				_g('Could not switch the Schoolyear!') . $e->getMessage());
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function settingsInputHandle() {

		$this->settingsInputCheck();
		$this->_highestGradelevel = $_POST['highestGradelevel'];
		$this->_shouldCreateClassesIfNotExist =
			isset($_POST['shouldCreateClassesIfNotExist']);
	}

	protected function settingsInputCheck() {

		require_once PATH_INCLUDE . '/gump.php';

		$gump = new GUMP();
		$gump->rules($this->_inputValidation);

		if($gump->run($_POST)) {
			return;
		}
		else {
			$this->_interface->dieError(
				$gump->get_readable_string_errors(true));

		}

	}

	protected function uploadStart() {

		TableMng::getDb()->autocommit(false);
	}

	protected function upload() {

		$this->activeSchoolyearChange();
		$this->usersTransferToIncrementedGradelevel();
	}

	protected function usersTransferToIncrementedGradelevel() {

		$existingEntries = $this->usersGradelevelToIncrementGet();
		$this->gradesSetRearrangedByLevel();

		$toUpgrade = $this->usersInGradesToAddGet($existingEntries,
			$this->_arrangedGrades);

		$this->usersNewGradelevelAndSchoolyearsUpload($toUpgrade);
	}

	/**
	 * Fetches the User-Entries of the active Schoolyear to Upgrade
	 *
	 * @return array The Users which gradelevel to increment
	 */
	protected function usersGradelevelToIncrementGet() {

		$toUpgrade = TableMng::query(
			'SELECT uigs.*, g.gradelevel AS currentGradelevel,
				g.label AS gradelabel
			FROM SystemUsersInGradesAndSchoolyears uigs
			JOIN SystemGrades g ON uigs.gradeId = g.ID
			WHERE uigs.schoolyearId = @activeSchoolyear');

		return $toUpgrade;
	}

	/**
	 * Fetches and specially arranges the Grades
	 *
	 * The grades first get arranged after the gradelevel. They contain
	 * multiple gradelabel => gradeId-Pairs. This helps the algorithm to work
	 * a bit faster
	 *
	 * @return array An specially arranged Array with all grades in it
	 */
	protected function gradesSetRearrangedByLevel() {

		$arrangedGrades = array();
		$grades = TableMng::query('SELECT * FROM SystemGrades');

		foreach($grades as $grade) {
			if(!isset($arrangedGrades[$grade['gradelevel']])) {
				$arrangedGrades[$grade['gradelevel']] = array();
			}
			$arrangedGrades[$grade['gradelevel']][$grade['label']] =
				$grade['ID'];
		}

		$this->_arrangedGrades = $arrangedGrades;
	}

	/**
	 * Searches for the GradeId by the gradelevel and gradelabel
	 * @param  string $level          The Level of the GradeId to search for
	 * @param  string $label          The Label of the GradeId to search for
	 * @param  array  $gradesToSearch The Haystack of grades to search in;
	 *                                Has to be arranged like in @see
	 *                                gradesGetRearrangedByLevel()
	 * @return string                 The ID of the Grade, or false on not
	 *                                found
	 */
	protected function gradeIdByGradeDataGet($level, $label, $gradesToSearch) {

		foreach($gradesToSearch as $tsGradelevel => $tsGrade) {
			if($tsGradelevel == $level) {
				if(!empty($tsGrade[$label])) {
					return $tsGrade[$label];
				}
				else {
					return false;
				}
			}
		}

		return false;
	}

	/**
	 * Searches and adds the Grade to which the User should get added
	 *
	 * @param  array  $toUpgrade An Array of Users to be upgraded
	 * @param  array  $grades    The Grades in which to search for GradeIds
	 * @return array             The updated tupUpgrade-Array
	 */
	protected function usersInGradesToAddGet($toUpgrade, $grades) {

		foreach($toUpgrade as &$userToUpgrade) {

			$userToUpgrade['nextGradelevel'] =
				(int)$userToUpgrade['currentGradelevel'] + 1;

			if($userToUpgrade['nextGradelevel'] <= $this->_highestGradelevel &&
				(int)$userToUpgrade['currentGradelevel'] !== 0) {
			}
		}

		return $toUpgrade;
	}

	protected function usersNewGradelevelAndSchoolyearsUpload($toUpload) {

		$stmt = TableMng::getDb()->prepare(
			'INSERT INTO SystemUsersInGradesAndSchoolyears
				(userId, gradeId, schoolyearId) VALUES (?, ?, ?)');

		foreach($toUpload as $newJoin) {

			if($newJoin['currentGradelevel'] === 0 ||
				$newJoin['nextGradelevel'] > $this->_highestGradelevel) {
				continue;
			}

			$newJoin = $this->gradeCreateIfMissingAndAllowed($newJoin);

			if(!empty($newJoin['newGradeId'])) {
				$stmt->bind_param('iii', $newJoin['userId'],
					$newJoin['newGradeId'], $this->_schoolyearId);
				if(!$stmt->execute()) {
					$this->_interface->dieError(_g('Could not add Users to their new Grade and Schoolyear!') . $stmt->error);
				}
			}
			else {
				$this->_interface->dieError(_g(
				'Grade %1$s-%2$s could not be found! You should add it to the Grades to allow users to be moved in there',
					$newJoin['nextGradelevel'],
					$newJoin['gradelabel'])
			);
			}
		}
	}

	/**
	 * Creates a Grade if it is missing and User wants it to be created
	 *
	 * @param  array  $data The Data of the Grade
	 * @return array        The data with the key "newGradeId" added if a
	 *                      Grade was added
	 */
	protected function gradeCreateIfMissingAndAllowed($data) {

		$newGradeId = $this->gradeIdByGradeDataGet(
					$data['nextGradelevel'],
					$data['gradelabel'],
					$this->_arrangedGrades);
		if($newGradeId !== false) {
			$data['newGradeId'] = $newGradeId;
		}
		else {
			if($this->_shouldCreateClassesIfNotExist) {
				$data['newGradeId'] = $this->gradeCreate(
					$data['gradelabel'],
					$data['nextGradelevel']);
				//Refresh the existing Grades
				$this->gradesSetRearrangedByLevel();
			}
		}

		return $data;
	}

	/**
	 * Commits the Grade-Creation to the Database
	 *
	 * @param  string $label The Name of the Grade
	 * @param  int    $level The Level of the Grade
	 * @return int           The ID of the Grade
 	 */
	protected function gradeCreate($label, $level) {

		TableMng::query("INSERT INTO SystemGrades (label, gradelevel) VALUES
			('$label', '$level')");

		return TableMng::getDb()->insert_id;
	}

	/**
	 * Changes the active Schoolyear to the new userselected Schoolyear
	 *
	 * @param  int    $newSyId The ID of the Schoolyear to activate
	 */
	protected function activeSchoolyearChange() {

		$newSyId = $this->_schoolyearId;

		TableMng::queryMultiple(
			"UPDATE SystemSchoolyears SET active = 0 WHERE active = 1;
			UPDATE SystemSchoolyears SET active = 1 WHERE ID = '$newSyId'");
	}

	/**
	 * Finalizes the Upload, commits it if there was no error
	 */
	protected function uploadFinish() {

		TableMng::getDb()->commit();
		TableMng::getDb()->autocommit(true);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_interface;

	protected $_inputValidation = array(
		'highestGradelevel' => array(
			'required|numeric|min_len,1|max_len,2',
			'sql_escape',
			'Höchster Jahrgang'
		)
	);

	protected $_highestGradelevel;

	protected $_schoolyearId;

	protected $__shouldCreateClassesIfNotExist;
}

?>
