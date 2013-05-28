<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysJointUsersInGrade extends TableManager {

		////////////////////////////////////////////////////////////////////////////////
		//Attributes
		////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////
		//Constructor
		////////////////////////////////////////////////////////////////////////////////
		public function __construct($interface = NULL) {
			parent::__construct('jointUsersInGrade');
		}

		////////////////////////////////////////////////////////////////////////////////
		//Getters and Setters
		////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////
		//Methods
		////////////////////////////////////////////////////////////////////////////////
		public function addJoint ($userID, $gradeID) {
			$this->addEntry('UserID', $userID, 'GradeID', $gradeID);
		}

		public function addMultipleJoint ($rows) {
			$this->doMultiQueryManagerByRows (DbMultiQueryManager::$Insert, $rows);
		}

		public function getAllJoints () {

			$joints = $this->getTableData();
			return $joints;
		}

		public function deleteJoint ($ID) {

			$this->delEntry($ID);
		}

		public function deleteJointsByUserId ($userID) {

			$this->deleteAllEntriesWithValueOfKey('UserID', $userID);
		}

		public function getJointByUserId ($userID) {

			$joint = $this->searchEntry('UserID = ' . $userID);
			return $joint;
		}

		public function getAllJointsOfGradeId ($gradeId) {

			$joints = $this->getTableData('GradeID=' . $gradeId);
			return $joints;
		}

		public function addMultipleJoints ($rows) {
			$this->doMultiQueryManagerByRows (DbMultiQueryManager::$Insert, $rows);
		}

 		////////////////////////////////////////////////////////////////////////////////
		//Implementations
		////////////////////////////////////////////////////////////////////////////////

}

?>