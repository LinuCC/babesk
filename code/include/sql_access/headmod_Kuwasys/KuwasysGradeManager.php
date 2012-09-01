<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysGradeManager extends TableManager {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_gradesToFetchArray;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct($interface = NULL) {
		parent::__construct('grade');
		$this->_gradesToFetchArray = array();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function addGrade ($label, $year) {
			
		parent::addEntry('label', $label, 'gradeValue', $year);
	}

	public function deleteGrade ($ID) {
			
		parent::delEntry($ID);
	}

	public function alterGrade ($ID, $label, $year) {
			
		parent::alterEntry($ID, 'label', $label, 'gradeValue', $year);
	}

	public function getAllGrades () {
			
		$grades = parent::getTableData();
		return $grades;
	}

	public function getGrade ($ID) {
			
		$grade = parent::searchEntry('ID=' . $ID);
		return $grade;
	}

	/**
	 * adds the Id to an array. the function getAllGradesOfGradeIdItemArray() will then fetch all of the items
	 * added from MySQL in a single query.
	 * @param unknown $gradeId
	 */
	public function addGradeIdItemToFetch ($gradeId) {
		
		$this->_gradesToFetchArray [] = $gradeId;
	}

	/**
	 * fetches all Grades that were added to the gradeIdItemArray beforehands with the function addGradeIdItemToFetch
	 * @return grades []
	 */
	public function getAllGradesOfGradeIdItemArray () {
		
		$grades = $this->getMultipleEntriesByArray('ID', $this->_gradesToFetchArray);
		return $grades;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>