<?php

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
			die();
			$this->uploadFinish();

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
			$_POST['shouldCreateClassesIfNotExist'];
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

		$this->usersTransferToIncrementedGradelevel();
	}

	protected function usersTransferToIncrementedGradelevel() {

		$existingEntries = $this->usersGradelevelToIncrementGet();
		$grades = $this->gradesGetRearrangedByLevel();

		$toUpgrade = $this->usersInGradesToAddGet($existingEntries, $grades);

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
			FROM usersInGradesAndSchoolyears uigs
			JOIN Grades g ON uigs.gradeId = g.ID
			WHERE schoolyearId = @activeSchoolyear');

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
	protected function gradesGetRearrangedByLevel() {

		$arrangedGrades = array();
		$grades = TableMng::query('SELECT * FROM Grades');

		foreach($grades as $grade) {
			if(!isset($arrangedGrades[$grade['gradelevel']])) {
				$arrangedGrades[$grade['gradelevel']] = array();
			}
			$arrangedGrades[$grade['gradelevel']][$grade['label']] =
				$grade['ID'];
		}

		return $arrangedGrades;
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
	 * Returns the GradeId for the new (incremented) Grade of the User
	 *
	 * @param  array  $userToUpgrade The User to Upgrade
	 * @param  array  $grades        The grades in which to search for the
	 *                               correct GradeId
	 * @return string                The Grade-ID of the new Grade
	 */
	protected function gradeIdNewGetForUser($userToUpgrade, $grades) {

		$newGradeId = $this->gradeIdByGradeDataGet(
			$userToUpgrade['nextGradelevel'],
			$userToUpgrade['gradelabel'],
			$grades);

		if(!$newGradeId) {
			$this->_interface->dieError(_g(
				'Grade %1$s-%2$s could not be found! You should add it to the Grades to allow users to be moved in there',
					$userToUpgrade['nextGradelevel'],
					$userToUpgrade['gradelabel'])
			);
		}

		return $newGradeId;
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
				$userToUpgrade['newGradeId'] = $this->gradeIdNewGetForUser(
					$userToUpgrade, $grades);
			}
		}

		return $toUpgrade;
	}

	protected function usersNewGradelevelAndSchoolyearsUpload($toUpload) {

		$stmt = TableMng::getDb()->prepare(
			'INSERT INTO usersInGradesAndSchoolyears
				(userId, gradeId, schoolyearId) VALUES (?, ?, ?)');

		foreach($toUpload as $newJoin) {

			$newJoin = $gradeCreateIfMissingAndAllowed($newJoin);

			/**
			 * HIER MUSS NOCH GETESTET WERDEN
			 */

			if(!empty($newJoin['newGradeId'])) {
				echo "HIN: $newJoin[currentGradelevel]-$newJoin[gradelabel] =>
				$newJoin[nextGradelevel]-$newJoin[gradelabel]<br />";
				$stmt->bind_param('iii', ,
					$data['newGradeId'], $this->_schoolyearId);
			}
			else {
				echo "___: $newJoin[nextGradelevel]-$newJoin[gradelabel]<br />";
			}
		}
	}

	protected function gradeCreateIfMissingAndAllowed($data) {

		if(empty($data['newGradeId']) &&
			$this->_shouldCreateClassesIfNotExist) {
			$data['newGradeId'] = $this->gradeCreate(
				$data['gradelabel'],
				$data['nextGradelevel']);
		}

		return $data;
	}

	protected function gradeCreate($label, $level) {

		TableMng::query("INSERT INTO Grades (label, gradelevel) VALUES
			($label, $level)");

		return TableMng::getDb()->last_insert_id;
	}

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
			'mysql_save',
			'Höchster Jahrgang'
		)
	);

	protected $_highestGradelevel;

	protected $_schoolyearId;

	protected $__shouldCreateClassesIfNotExist;
}

?>
