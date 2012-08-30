<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysJointUsersInClass extends TableManager {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_statusActiveStr = 'active';
	private $_statusWaitingStr = 'waiting';
	private $_statusFirstRequestStr = 'request#1';
	private $_statusSecondRequestStr = 'request#2';

	private $_multipleJointChanges;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface = NULL) {
		parent::__construct('jointUsersInClass');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function getActiveStatusString () {
		return $this->_statusActiveStr;
	}

	public function getWaitingStatusString () {
		return $this->_statusWaitingStr;
	}


	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////

	public function getAllJoints () {
		$joints = $this->getTableData();
		return $joints;
	}

	public function getAllJointsWithStatusActive () {
		$joints = $this->getTableData(sprintf('status="%s"', $this->_statusActiveStr));
		return $joints;
	}

	public function getAllJointsWithStatusWaiting () {
		$joints = $this->getTableData(sprintf('status="%s"', $this->_statusWaitingStr));
		return $joints;
	}

	public function getAllJointsWithStatusRequestFirst () {
		$joints = $this->getTableData(sprintf('status="%s"', $this->_statusFirstRequestStr));
		return $joints;
	}

	public function getAllJointsWithStatusRequestSecond () {
		$joints = $this->getTableData(sprintf('status="%s"', $this->_statusSecondRequestStr));
		return $joints;
	}

	public function getAllJointsWithStatusActiveAndUserId ($userId) {
		$joints = $this->getTableData(sprintf('status="%s" AND UserID="%s"', $this->_statusActiveStr, $userId));
		return $joints;
	}

	public function getAllJointsWithStatusWaitingAndUserId ($userId) {
		$joints = $this->getTableData('status="'. $this->_statusWaitingStr .'" AND UserID=' . $userId);
		return $joints;
	}

	public function addJoint ($userId, $classId, $status) {
		$this->addEntry('UserID', $userId, 'ClassID', $classId, 'status', $status);
	}
	public function addJointWithStatusActive ($userId, $classId) {
		$this->addEntry('UserID', $userId, 'ClassID', $classId, 'status', $this->_statusActiveStr);
	}
	public function addJointWithStatusWaiting () {
		$this->addEntry('UserID', $userId, 'ClassID', $classId, 'status', $this->_statusWaitingStr);
	}
	public function addJointWithStatusRequestFirst () {
		$this->addEntry('UserID', $userId, 'ClassID', $classId, 'status', $this->$_statusFirstRequestStr);
	}
	public function addJointWithStatusRequestSecond () {
		$this->addEntry('UserID', $userId, 'ClassID', $classId, 'status', $this->$_statusSecondRequestStr);
	}

	public function getAllJointsOfUserId ($userId) {
		$joints = $this->getTableData('UserID=' . $userId);
		return $joints;
	}

	public function getCountOfActiveUsersInClass ($classId) {

		$joints = $this->getTableData('ClassID=' . $classId);
		$counter = 0;
		foreach ($joints as $joint) {
			if($joint ['status'] == $this->_statusActiveStr) {
				$counter ++;
			}
		}
		return $counter;
	}

	public function getAllJointsWithClassId ($classId) {
		
		$joints = $this->getTableData('ClassID=' . $classId);
		return $joints;
	}

	public function getJointOfUserIdAndClassId ($userId, $classId) {

		$joint = $this->getTableData(sprintf('ClassID=%s AND UserID=%s', $classId, $userId));
		if(count($joint) > 1) {
			throw new OutOfBoundsException('There are more than one joints for this userId and ClassId!');
		}
		return $joint [0];
	}
	
	public function isJointExistingByUserIdAndClassId ($userId, $classId) {
		
		try {
			$this->searchEntry(sprintf('UserID="%s" AND ClassID="%s"', $userId, $classId));
		} catch (MySQLVoidDataException $e) {
			return false;
		} catch (Exception $e) {
			throw $e;
		}
		return true;
	}

	public function alterStatusOfJoint ($jointId, $status) {
		$this->alterEntry($jointId, 'status', $status);
	}
	public function alterJoint ($jointId, $classId, $userId, $status) {
		$this->alterEntry($jointId, 'status', $status, 'ClassID', $classId, 'UserID', $userId);
	}

	public function deleteJoint ($jointId) {
		$this->delEntry($jointId);
	}

	public function deleteAllJointsOfClassId ($classId) {

		$this->deleteAllEntriesWithValueOfKey('ClassID', $classId);
	}

	public function getJointsByIdArray ($idArray) {

		$joints = $this->getEntriesOfIds($idArray);
		return $joints;
	}

	public function alterStatusOfJointAddEntryToTempList ($jointId, $status) {

		$this->_multipleJointChanges [] = array('jointId' => $jointId, 'status' => $status);
	}

	public function upAlterStatusOfJointTempListToDatabase () {

		if(!isset($this->_multipleJointChanges) || !count ($this->_multipleJointChanges)) {
			throw new Exception ('Add some changes first!');
		}

		$valueChanges = '';
		foreach ($this->_multipleJointChanges as $jointChange) {
			$valueChanges .= sprintf('(%s,"%s"),', $jointChange ['jointId'], $jointChange ['status']);
		}
		$valueChanges = rtrim($valueChanges, ',');
		$query = sql_prev_inj(sprintf('INSERT INTO %s (ID, status) VALUES %s ON DUPLICATE KEY UPDATE ID=VALUES(ID),status=VALUES(status);',
				 $this->tablename, $valueChanges));
		$this->executeQuery($query);
	}
	
	public function getAllJointsOfClassIdAndStatusActive ($classId) {
		
		$sqlPartString = sprintf('ClassID="%s" AND status="%s"', $classId, $this->_statusActiveStr);
		$joints = $this->getTableData($sqlPartString);
		return $joints;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>