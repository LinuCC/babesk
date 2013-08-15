<?php

require_once PATH_INCLUDE . '/CsvImport.php';

/**
 * A CSV-Importer. Contains various useful methods to convert names to IDs
 *
 * Allows to convert for example schoolyear-names to schoolyearIds
 */
class CsvImportTableData extends CsvImport {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::execute($dataContainer);
		$this->_acl = $dataContainer->getAcl();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Tries to get the ID of given Schoolyearnames allowing to upload it
	 *
	 * Dies displaying a message on Error
	 * Adds the pair 'ID' => <schoolyearId> to each array-Element
	 */
	protected function schoolyearIdsAppendToColumns() {

		$schoolyears = $this->schoolyearsGetAll();
		foreach($this->_contentArray as &$con) {

			if(!empty($con['schoolyear'])) {
				$id = $this->schoolyearIdGetByLabel(
					$con['schoolyear'], $schoolyears);

				if($id !== false) {
					$con['schoolyearId'] = $id;
				}
				else {
					$this->errorDie(
						_g('Could not find the Schoolyear "%1$s"',
							$con['schoolyear']));
				}
			}
		}
	}

	/**
	 * Fetches all Schoolyears and returns them
	 *
	 * @return array  The fetched Schoolyears
	 */
	private function schoolyearsGetAll() {

		$schoolyears = TableMng::query('SELECT * FROM schoolYear');

		return $schoolyears;
	}

	/**
	 * Returns the Schoolyear-ID of the Schoolyear that has the Label
	 *
	 * @param  string $name        The Label of the Schoolyear to search for
	 * @param  array  $schoolyears The Schoolyears to search in
	 * @return string              The ID if found, else false
	 */
	private function schoolyearIdGetByLabel($name, $schoolyears) {

		foreach ($schoolyears as $schoolyear) {
			if($schoolyear['label'] == $name) {
				return $schoolyear['ID'];
			}
		}

		return false;
	}


	/**
	 * Adds Grade-IDs to the elements of they contain grade-names
	 *
	 * Dies displaying a Message on Error
	 * Uses 'grade' => <gradevalue>
	 * Adds 'gradeId' => <gradeId>
	 */
	protected function gradeIdsAppendToColumns() {

		$allGrades = TableMng::query('SELECT ID,
			CONCAT(g.gradelevel, "-", LOWER(g.label)) AS name FROM Grades g');
		$flatGrades = ArrayFunctions::arrayColumn($allGrades, 'name', 'ID');

		foreach($this->_contentArray as &$con) {

			$grade = $con['grade'];
			if(!empty($grade)) {

				$id = array_search(strtolower($grade), $flatGrades);
				if($id !== false) {
					$con['gradeId'] = $id;
				}
				else {
					$this->errorDie(
						_g('Could not find the Grade "%1$s"', $grade));
				}
			}
		}
	}

	/**
	 * Returns the ID of the "NoGrade"-Grade
	 *
	 * Dies if Grade not found or multiple Entries returned
	 *
	 * @return string The ID of the Grade
	 */
	protected function noGradeIdGet() {

		try {
			$row = TableMng::querySingleEntry('SELECT ID FROM Grades
				WHERE gradelevel = 0');

		} catch(MultipleEntriesException $e) {
			$this->errorDie(_g('Multiple Grades with gradelevel "0" found!'));

		} catch (Exception $e) {
			$this->errorDie(
				_g('Could not find the ID of the "NoGrade"-Grade'));
		}

		return $row['ID'];
	}

	/**
	 * Checks if the given Headmodules are enabled or not
	 *
	 * @param  array  $headmodules The Headmodules to check for
	 * @return array               The given Array, but each element has a
	 *     boolean value given to it stating if the Heamodule exists & is
	 *     activated or not
	 */
	protected function enabledHeadmodulesCheck(array $headmodules) {

		$moduleroot = $this->_acl->getModuleroot();
		foreach($headmodules as $name => $mod) {
			$act = $moduleroot->moduleByPathGet('administrator/' . $mod));
			$headmodules[$name] = $act;
		}

		return $headmodules;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The AccessControlLayer used to check if the Headmodules are enabled
	 * @var Acl
	 */
	private $_acl;

}

?>
