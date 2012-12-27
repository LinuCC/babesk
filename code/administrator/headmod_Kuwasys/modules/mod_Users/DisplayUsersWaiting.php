<?php
class DisplayUsersWaiting {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface, $languageManager) {

		$this->_interface = $interface;
		$this->_languageManager = $languageManager;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute () {

		$this->initManagers ();
		$this->initDataArrays ();
		$this->filterAndAddData ();
		$this->sortFilterUsers ();
		$this->showUsersWaiting ();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	private function initManagers () {

		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointClassTeacherInClass.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysClassTeacherManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersInClassStatusManager.php';
		require_once PATH_ADMIN . '/headmod_Kuwasys/KuwasysDatabaseAccess.php';

		$this->_userManager = new KuwasysUsersManager();
		$this->_classManager = new KuwasysClassManager();
		$this->_classteacherManager = new KuwasysClassTeacherManager();
		$this->_jointClassteacherInClassManager = new KuwasysJointClassTeacherInClass();
		$this->_usersInClassStatusManager = new KuwasysUsersInClassStatusManager ();
		$this->_jointUsersInClassManager = new KuwasysJointUsersInClass();
		$this->_databaseAccessManager = new KuwasysDatabaseAccess($this->_interface);
	}

	private function initDataArrays () {

		$this->_jointsUsersInClassWaiting = $this->getAllJointsUsersInClassWaiting();
		$this->_users = $this->getUsersByJoints($this->_jointsUsersInClassWaiting);
		$this->_classes = $this->getClassesByJointsUsersInClassWithoutDieing($this->_jointsUsersInClassWaiting);
		if(is_array($this->_classes)) {
			$this->_jointsUsersInClassActive = $this->getAllJointsUsersInClassWithStatusActiveWithoutDieing();
			$this->_jointsClassteacherInClass = $this->getJointsClassteacherInClassByClassesWithoutDieing($this->_classes);
			if(is_array($this->_jointsClassteacherInClass)) {
				$this->_classteachers = $this->getClassteachersByJointsClassteacherInClass($this->_jointsClassteacherInClass);
			}
		}
	}

	private function filterAndAddData () {

		foreach ($this->_users as &$user) {
			foreach ($this->_jointsUsersInClassWaiting as $joint) {
				if($joint ['UserID'] == $user ['ID']) {
					$this->filterCorrectClasses($user, $joint);
				}
			}
		}
	}

	private function sortFilterUsers () {
		$users = $this->_users;
		try {
			$users = KuwasysFilterAndSort::elementsFilter ($users);
			$users = KuwasysFilterAndSort::elementsSort ($users);
		} catch (Exception $e) {
			$this->_interface->showMsg ("Konnte die Benutzer nicht nach den angegebenen Kriterien filtern");
			return;
		}
		$this->_users = $users;
	}

	private function filterCorrectClasses (&$user, $jointUsersInClassWaiting) {

		foreach ($this->_classes as &$class) {
			if($class ['ID'] == $jointUsersInClassWaiting ['ClassID']) {
				$this->addClassItemsToUser($user, $class);
			}
		}
	}

	private function addClassItemsToUser (&$user, $class) {

		if(is_array($this->_jointsUsersInClassActive)) {
			$class ['activeParticipants'] = $this->getCountOfActiveUsersByClassId($class ['ID']);
		}
		if(is_array($this->_classteachers)) {
			$user ['classteachers'] [] = $this->getClassteacherByClassIdWithFetchedArrays($class ['ID']);
		}
		$user ['classes'] [] = $class;
	}

	private function showUsersWaiting () {

		$this->_interface->showUsersWaiting($this->_users);
	}

	/********************
	 * Access To Database
	********************/

	private function getAllJointsUsersInClassWaiting () {

		try {
			$status = $this->_usersInClassStatusManager->statusGetByName ('waiting');
			$joints = $this->_jointUsersInClassManager->getAllJointsWithStatusId($status ['ID']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoJointsUsersInClassWaiting'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointsUsersInClassWaiting'));
		}
		return $joints;
	}

	private function getUsersByJoints ($jointsUsersInClass) {

		foreach ($jointsUsersInClass as $joint) {
			$this->_databaseAccessManager->userIdAddToUserIdArray($joint ['UserID']);
		}
		$users = $this->_databaseAccessManager->userGetByUserIdArray();
		return $users;
	}

	private function getClassteacherByClassIdWithFetchedArrays ($classId) {

		if(is_array($this->_jointsClassteacherInClass)) {
			foreach ($this->_jointsClassteacherInClass as $joint) {
				if($joint ['ClassID'] == $classId) {
					foreach ($this->_classteachers as $classteacher) {
						if($classteacher ['ID'] == $joint ['ClassTeacherID']) {
							return $classteacher;
						}
					}
				}
			}
		}
	}

	private function getCountOfActiveUsersByClassId ($classId) {

		$activeUsers = 0;
		if(is_array($this->_jointsUsersInClassActive)
			&& count ($this->_jointsUsersInClassActive)) {
			foreach ($this->_jointsUsersInClassActive as $joint) {
				if($joint ['ClassID'] == $classId) {
					$activeUsers++;
				}
			}
		}
		return $activeUsers;
	}

	private function getAllJointsUsersInClassWithStatusActiveWithoutDieing () {

		try {
			$status = $this->_usersInClassStatusManager->statusGetByName ('active');
			$jointsUsersInClassActive = $this->_jointUsersInClassManager->getAllJointsWithStatusId ($status ['ID']);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('errorNoJointUsersInClassActive'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointUsersInClassActive'));
		}
		if (isset($jointsUsersInClassActive)) {
			return $jointsUsersInClassActive;
		}
	}

	private function getClassteachersByJointsClassteacherInClass ($jointsClassteacherInClass) {

		$classteacherIdArray = array();
		foreach ($jointsClassteacherInClass as $joint) {
			$classteacherIdArray [] = $joint ['ClassTeacherID'];
		}
		try {
			$classteachers = $this->_classteacherManager->getClassteachersByClassteacherIdArray($classteacherIdArray);
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClassteacher'));
		}
		return $classteachers;
	}

	private function getJointsClassteacherInClassByClassesWithoutDieing ($classes) {

		$classIdArray = array();
		foreach ($classes as $class) {
			$classIdArray [] = $class ['ID'];
		}
		try {
			$joints = $this->_jointClassteacherInClassManager->getJointsByClassIdArray($classIdArray);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showError($this->_languageManager->getText('errorNoClassteachersForClasses'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointsClassteacherInClass'));
		}
		if(isset($joints))
			return $joints;
	}

	private function getClassesByJointsUsersInClassWithoutDieing ($joints) {

		$classIdArray = array();
		foreach ($joints as $joint) {
			$classIdArray [] = $joint ['ClassID'];
		}
		try {
			$classes = $this->_classManager->getClassesByClassIdArray($classIdArray);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showError($this->_languageManager->getText('errorNoClassesForWaitingUsers'));
		} catch (Exception $e) {
			$this->_interface->showError(sprintf($this->_languageManager->getText('errorFetchClassesFromDatabase'), $e->getMessage()));
		}
		return $classes;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_classteacherManager;
	private $_jointClassteacherInClassManager;
	private $_jointUsersInClassManager;
	private $_userManager;
	private $_classManager;

	private $_jointsUsersInClassWaiting;
	private $_jointsUsersInClassActive;
	private $_jointsClassteacherInClass;
	private $_usersInClassStatusManager;
	private $_classteachers;
	private $_users;
	private $_classes;

	private $_databaseAccessManager;

	private $_interface;
	private $_languageManager;
}


?>