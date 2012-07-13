<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysJointClassInSchoolYearManager extends TableManager {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface = NULL) {
		parent::__construct('jointClassInSchoolYear');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function addJoint ($schoolYearID, $classID) {
		parent::addEntry('SchoolYearID', $schoolYearID, 'ClassID', $classID);
	}

	public function deleteAllJointsOfClass ($classID) {

		$classJoints = parent::getTableData('ClassID=' . $classID);

		if (!isset($classJoints) || !count($classJoints)) {
			throw new MySQLVoidDataException('No Joints are availabe');
		}
		foreach ($classJoints as $classJoint) {
			parent::delEntry($classJoint['ID']);
		}
	}
	
	public function getSchoolYearIdOfClassId ($classID) {
		
		$entry = parent::searchEntry('ClassID=' . $classID);
		return $entry ['SchoolYearID'];
	}
	
	public function alterSchoolYearIdOfClassId ($classID, $schoolYearId) {
		
		$entry = parent::searchEntry('ClassID=' . $classID);
		parent::alterEntry($entry ['ID'], 'SchoolYearID', $schoolYearId);
	}
	
	public function getAllJoints () {
		$joints = parent::getTableData ();
		return $joints;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>