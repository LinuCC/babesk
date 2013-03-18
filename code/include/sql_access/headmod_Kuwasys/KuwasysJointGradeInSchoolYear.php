<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysJointGradeInSchoolYear extends TableManager {

		////////////////////////////////////////////////////////////////////////////////
		//Attributes
		////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////
		//Constructor
		////////////////////////////////////////////////////////////////////////////////
		public function __construct($interface = NULL) {
			parent::__construct('jointGradeInSchoolYear');
		}

		////////////////////////////////////////////////////////////////////////////////
		//Getters and Setters
		////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////
		//Methods
		////////////////////////////////////////////////////////////////////////////////
		public function addJoint ($gradeId, $schoolyearId) {

			$this->addEntry('GradeID', $gradeId, 'SchoolYearID', $schoolyearId);
		}

		public function addMultipleJoints ($rows) {
			$dbMng = $this->getMultiQueryManager ();
			foreach ($rows as $row) {
				$dbMng->rowAdd ($row);
			}
			$dbMng->dbExecute (DbMultiQueryManager::$Insert);
		}

		public function deleteJointByGradeId ($id) {
			$this->deleteAllEntriesWithValueOfKey('GradeID', $id);
		}

		public function deleteJoint ($id) {

			$this->delEntry($id);
		}

		public function getAllJoints () {
			$joints = $this->getTableData();
			return $joints;
		}

		public function getJointByGradeId ($gradeId) {
			$joint = $this->searchEntry('GradeID=' . $gradeId);
			return $joint;
		}

		public function getSchoolyearIdOfGradeId ($gradeId) {

			$joint = $this->getJointByGradeId($gradeId);
			return $joint ['SchoolYearID'];
		}

		public function getAllJointsOfSchoolyearId ($schoolyearId) {

			$joints = $this->getTableData(sprintf('SchoolYearID="%s"', $schoolyearId));
			return $joints;
		}
		////////////////////////////////////////////////////////////////////////////////
		//Implementations
		////////////////////////////////////////////////////////////////////////////////

}

?>