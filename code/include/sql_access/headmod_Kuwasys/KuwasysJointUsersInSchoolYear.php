<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysJointUsersInSchoolYear extends TableManager {

		////////////////////////////////////////////////////////////////////////////////
		//Attributes
		////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////
		//Constructor
		////////////////////////////////////////////////////////////////////////////////
		public function __construct($interface = NULL) {
			parent::__construct('jointUsersInSchoolYear');
		}

		////////////////////////////////////////////////////////////////////////////////
		//Getters and Setters
		////////////////////////////////////////////////////////////////////////////////

		////////////////////////////////////////////////////////////////////////////////
		//Methods
		////////////////////////////////////////////////////////////////////////////////

		public function addJoint ($UserID, $SchoolYearID) {

			$this->addEntry('UserID', $UserID, 'SchoolYearID', $SchoolYearID);
		}

		public function deleteJointByUserId ($userId) {

			$this->deleteAllEntriesWithValueOfKey('UserID', $userId);
		}

		public function getSchoolYearIdByUserId ($userID) {

			$jointId = $this->getIDByValue('UserID', $userID);
			$schoolyearId = $this->getEntryValue($jointId, 'SchoolYearID');
			return $schoolyearId;
		}

		public function getAllJoints () {
			$joints = $this->getTableData();
			return $joints;
		}

		public function getAllJointsOfSchoolyearId ($id) {
			$joints = $this->getTableData (sprintf('SchoolYearID = "%s"', $id));
			return $joints;
		}

		////////////////////////////////////////////////////////////////////////////////
		//Implementations
		////////////////////////////////////////////////////////////////////////////////

}

?>