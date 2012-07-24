<?php

require_once PATH_ACCESS . '/TableManager.php';

class KuwasysJointUsersInClass extends TableManager {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_statusActiveStr = 'active';
	private $_statusWaitingStr = 'waiting';
	private $_statusRequestStr = 'request';

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface = NULL) {
		parent::__construct('jointUsersInClass');
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

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

	public function getAllJointsWithStatusRequest () {
		$joints = $this->getTableData(sprintf('status="%s"', $this->_statusRequestStr));
		return $joints;
	}

	public function getAllJointsWithStatusActiveAndUserId ($userId) {
		$joints = $this->getTableData(sprintf('status="%s" AND UserID="%s"', $this->_statusActiveStr, $userId));
		return $joints;
	}

	public function getAllJointsWithStatusWaitingAndUserId ($userId) {
		$joints = $this->getTableData('status="waiting" AND UserID=' . $userId);
		return $joints;
	}

	public function getAllJointsWithStatusRequestAndUserId ($userId) {
		$joints = $this->getTableData('status="request" AND UserID=' . $userId);
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
	public function addJointWithStatusRequest () {
		$this->addEntry('UserID', $userId, 'ClassID', $classId, 'status', $this->_statusRequestStr);
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
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

}

?>