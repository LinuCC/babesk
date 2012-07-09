<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysSchoolYearManager extends TableManager {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface = NULL) {
		parent::__construct('schoolYear');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * adds a new SchoolYear to the Database
	 * @param unknown_type $label
	 * @param boolean $activ if true, the SchoolYear which is active at the moment gets deactivated and
	 * this SchoolYear gets activated instead
	 */
	public function addSchoolYear ($label, $active) {

		var_dump($active);
		if ($active) {
			$this->deactivateSchoolYear(true);
		}
		parent::addEntry('label', $label, 'active', $active);
	}

	/**
	 * Deactivates the SchoolYear thats active at the moment
	 * @param boolean $ignoreNoActiveSchoolYear if true, it does not throw an error if no active SchoolYear found
	 */
	public function deactivateSchoolYear ($ignoreNoActiveSchoolYear = false) {

		try {
			$schoolYear = $this->getActiveSchoolYear();
		} catch (MySQLVoidDataException $e) {
			if (!$ignoreNoActiveSchoolYear) {
				throw new Exception('No active SchoolYear found. Could not deactivate SchoolYear');
			}
		}
		if(isset($schoolYear)) {
			parent::alterEntry($schoolYear['ID'], 'active', 'false');
		}
	}
	
	public function activateSchoolYear ($ID) {
		
		$this->deactivateSchoolYear(true);
		$this->alterEntry($ID, 'active', '1');
	}

	/**
	 * Returns the active SchoolYear
	 */
	public function getActiveSchoolYear () {

		$schoolYear = parent::searchEntry('active = true');
		return $schoolYear;
	}
	
	public function getAllSchoolYears () {
		
		$schoolYears = parent::getTableData();
		return $schoolYears;
	}
	
	public function getSchoolYear ($ID) {
		
		$schoolYear = parent::searchEntry('ID=' . $ID);
		return $schoolYear; 
	}
	
	public function alterSchoolYear ($ID, $label, $active) {
		
		$this->alterEntry($ID, 'label', $label, 'active', $active);
	}
	
	public function deleteSchoolYear ($ID) {
		
		$this->delEntry($ID);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>