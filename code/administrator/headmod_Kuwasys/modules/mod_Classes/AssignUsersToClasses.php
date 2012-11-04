<?php

/**
 * This Class contains the algorythm to assign Users to the requested Class of theirs.
 * It handles things like too many Users requested a Class, Users that are on the waiting-list etc.
 *
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class AssignUsersToClasses {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($interface, $languageManager, $users = NULL) {

		$this->_interface = $interface;
		$this->_languageManager = $languageManager;
		$this->databaseManagersInit();
		$this->_users = (isset($users)) ? $users : $this->UsersGetAll();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////

	public function execute () {

		if(isset($_POST['confirmed'])) {
			$this->usersToClassesAssignShowConfirmationDialog();
		}
		else if (isset($_POST['jointChangesConfirmed'])) {
			$this->usersToClassesAssignToDatabase ();
		}
		else {
			$this->menuDisplay();
		}
	}

	public function usersToClassesAssignShowConfirmationDialog () {

		$classes = $this->classGetAll();
		$jointsUsersInClass = $this->jointsUsersInClassHandle();
		$combinedRequests = $this->combinedRequestsInit($classes, $jointsUsersInClass);
		$requestsPassed = array(); //The Requests that are passed, so the Users get assigned to the classes
		$requestsNotPassed = array(); //All of the Requests that havent passed, so the User gets on the waiting-list

		///@FIXME: freeslots has to know how many people already are in the class!
		foreach ($classes as $class) {
			$freeSlots = (int) $class ['maxRegistration'];
			foreach($combinedRequests as $requestsOfOneType) {
				//The Primary Requests getting looped first because of elementorder of array in combinedRequestsInit()
				if(!isset($requestsOfOneType [$class ['ID']])) {
					continue;
				}
				else if ($freeSlots <= 0) {
					$this->requestsPassNoOf($requestsOfOneType [$class ['ID']], $requestsNotPassed);
					continue 2;
				}
				$this->requestsPass($requestsOfOneType[$class ['ID']], $requestsPassed, $requestsNotPassed, $freeSlots);
			}
		}
		$this->requestsTemporarilySaveInSession($requestsPassed, $requestsNotPassed);
		$this->changesToDatabaseShowConfirmationDialog($requestsPassed, $requestsNotPassed);
	}

	public function usersToClassesAssignToDatabase () {

		$requestsPassed = $this->requestsPassedFetchFromSession();
		$requestsNotPassed = $this->requestsNotPassedFetchFromSession();

		foreach ($requestsPassed as $request) {
			$this->jointUsersInClassSetToActiveAddToMultipleChangesList($request ['jointId']);
		}
		foreach ($requestsNotPassed as $request) {
			$this->jointUsersInClassSetToWaitingAddToMultipleChangesList($request ['jointId']);
		}
		$this->jointUsersInClassUploadMultipleChangesList();
		$this->_interface->dieMsg($this->_languageManager->getText('finishedAssignRequestsOfUsers'));
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function databaseManagersInit () {

		require_once PATH_ACCESS_KUWASYS . '/KuwasysJointUsersInClass.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysClassManager.php';
		require_once PATH_ACCESS_KUWASYS . '/KuwasysUsersInClassStatusManager.php';

		$this->_usersManager = new KuwasysUsersManager();
		$this->_classManager = new KuwasysClassManager();
		$this->_jointUsersInClassManager = new KuwasysJointUsersInClass();
		$this->_usersInClassStatusManager = new KuwasysUsersInClassStatusManager ();
	}

	/**
	 * This function filters elements to the arrays requestsPassed and requestsNotPassed depending on the arguments given
	 * @param array() $requests the requests of Users for Classes
	 * @param array() $requestsPassed the Array to put passed requests in; Call-by-Reference'd
	 * @param array() $requestsNotPassed the Array to put not passed requests in; Call-by-Reference'd
	 * @param int $freeSlots The free Slots of the Class, maximum count of elements to add to the requestsPassed-array; Call-by-Reference'd
	 */
	private function requestsPass ($requests, &$requestsPassed, &$requestsNotPassed, &$freeSlots) {

		$regCount = count($requests);
		if(count($requests) <= $freeSlots) {
			$requestsPassed = $this->requestsPassAllOf($requests, $requestsPassed);
			$freeSlots -= $regCount;
		}
		else {
			echo $freeSlots;
			$this->requestsPassRandom($requests, $requestsPassed, $requestsNotPassed, $freeSlots);
			$freeSlots = 0;
		}
	}

	private function jointUsersInClassSetToActiveAddToMultipleChangesList ($jointId) {

		try {
			$this->_jointUsersInClassManager->alterStatusOfJointAddEntryToTempList($jointId, 'active');
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorJointUsersInClassChange'));
		}
	}

	private function jointUsersInClassSetToWaitingAddToMultipleChangesList ($jointId) {

		try {
			$this->_jointUsersInClassManager->alterStatusOfJointAddEntryToTempList($jointId, 'waiting');
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorJointUsersInClassChange'));
		}
	}

	private function jointUsersInClassUploadMultipleChangesList () {

		try {
			$this->_jointUsersInClassManager->upAlterStatusOfJointTempListToDatabase();
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorJointUsersInClassChangeToDatabase') . $e->getMessage());
		}
	}

	private function requestsPassNoOf ($requests, &$requestsNotPassed) {

		foreach ($requests as $request) {
			if(is_array($request)) {
				$this->requestArrayAdd($requestsNotPassed, $request ['UserID'], $request ['ClassID'], $request ['ID']);
			}
		}
	}

	private function requestsTemporarilySaveInSession ($requestsPassed, $requestsNotPassed) {

		$_SESSION ['requestsPassed'] = $requestsPassed;
		$_SESSION ['requestsNotPassed'] = $requestsNotPassed;
	}

	private function requestsPassedFetchFromSession () {

		return  $_SESSION ['requestsPassed'];
	}

	private function requestsNotPassedFetchFromSession () {

		return  $_SESSION ['requestsNotPassed'];
	}

	private function requestsPassAllOf ($requests, $requestsPassed) {

		foreach ($requests as $request) {
			//There are some single elements like maxRegistration, filter them out
			if(is_array($request)) {
				$this->requestArrayAdd($requestsPassed, $request ['UserID'], $request ['ClassID'], $request ['ID']);
			}
		}
		return $requestsPassed;
	}

	/**
	 *
	 * @param unknown $requests
	 * @param unknown $requestsPassed
	 * @param unknown $requestsNotPassed
	 * @param unknown $countOfPasses
	 */
	private function requestsPassRandom ($requests, &$requestsPassed, &$requestsNotPassed, $countOfPasses) {

		$randomNumberArray = $this->randomNumberArrayGenerate(1, count($requests), $countOfPasses);

		foreach ($randomNumberArray as $randomCounterPosition) {
			$loopCounter = 0;
			foreach ($requests as &$request) {
				if($loopCounter == $randomCounterPosition) {
					$this->requestArrayAdd($requestsPassed, $request ['UserID'], $request ['ClassID'], $request ['ID']);
					$request ['isAdded'] = true;
					continue 2;
				}
				$loopCounter++;
			}
		}

		//Do NOT remove the Reference to request, else buggy behaviour of algorythm!
		foreach ($requests as &$request) {
			if(!isset($request ['isAdded']) || !$request ['isAdded']) {
				$this->requestArrayAdd($requestsNotPassed, $request ['UserID'], $request ['ClassID'], $request ['ID']);
			}
		}
	}

	/**
	 * Adds an array with userId and classId in it to the requestsPassed-array
	 * @param array() $requestsPassed Call-by-Reference'd!
	 * @param string $userId
	 * @param string $classId
	 */
	private function requestArrayAdd (&$requestsArray, $userId, $classId, $jointId) {

		$requestsArray [] = array(
				'userId' => $userId,
				'classId' => $classId,
				'jointId' => $jointId);
	}

	private function changesToDatabaseShowConfirmationDialog ($requestsPassed, $requestsNotPassed) {

		$users = $this->usersGetAll();
		$classes = $this->classGetAll();

		foreach ($requestsPassed as &$request) {
			$this->requestWithUsernameExtend($request, $users);
			$this->requestWithClassnameExtend($request, $classes);
		}
		foreach ($requestsNotPassed as &$request) {
			$this->requestWithUsernameExtend($request, $users);
			$this->requestWithClassnameExtend($request, $classes);
		}

		$this->_interface->showConfirmDialogAssignUsersToClass($requestsPassed, $requestsNotPassed);
	}

	private function requestWithUsernameExtend (&$request, $users) {

		/**
		 * @todo foreach very slow, make it faster
		 */
		foreach ($users as $user) {
			if($user ['ID'] == $request ['userId']) {
				$request ['username'] = $user ['forename'] . ' ' . $user ['name'];
			}
		}
	}

	private function requestWithClassnameExtend (&$request, $classes) {

		/**
		 * @todo foreach very slow, make it faster
		 */
		foreach ($classes as $class) {
			if($class ['ID'] == $request ['classId']) {
				$request ['classname'] = $class ['label'];
			}
		}
	}

	/**
	 * Picks numbers out of a number-range (from $min to $max) and put them into an Array, until the Array has
	 * $countofValues values. Each number is unique.
	 * @param int $min
	 * @param int $max
	 * @param int $countOfValues
	 * @return array()
	 */
	private function randomNumberArrayGenerate ($min, $max, $countOfValues) {

		$usableNumbers = array();
		$finishedNumberArray = array();
		for ($i = $min; $i <= $max; $i++) {
			$usableNumbers [] = $i;
		}
		$finishedNumberArray = array_rand($usableNumbers, $countOfValues);

		if(!is_array($finishedNumberArray)) {
			$finishedNumberArray = array(0 => $finishedNumberArray);
		}

		return $finishedNumberArray;
	}

	private function combinedRequestsInit ($classes, $jointsUsersInClass) {

		$combinedRequests = array();
		$requestStrArray = array('request#1', 'request#2');
		foreach ($requestStrArray as $requestStr) {
			if(!isset($jointsUsersInClass [$requestStr])) {
				continue;
			}
			// 			$combinedRequests = $this->combinedRequestsSetMaxRegistrationOfClass($classes, $combinedRequests, $requestStr);
			foreach ($jointsUsersInClass [$requestStr] as $joint) {
				foreach ($classes as $class) {
					if($joint ['ClassID'] == $class ['ID']) {
						$combinedRequests [$requestStr] [$class ['ID']] [$joint ['ID']] = $joint;
						continue 2;
					}
				}
			}
		}
		return $combinedRequests;
	}

	// 	private function combinedRequestsSetMaxRegistrationOfClass ($classes, $combinedRequests, $requestStr) {

	// 		foreach($classes as $class) {
	// 			$combinedRequests [$requestStr] [$class ['ID']] ['maxRegistration'] = $class ['maxRegistration'];
	// 		}
	// 		return $combinedRequests;
	// 	}

	private function jointsUsersInClassHandle () {

		try {
			$jointsUsersInClass ['request#1'] = $this->jointsUsersInClassFirstRequestGetAndThrowWhenVoid();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('errorNoJointsUsersInClassFirstRequest'));
		}
		try {
			$jointsUsersInClass ['request#2'] = $this->jointsUsersInClassSecondRequestGetAndThrowWhenVoid();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->showMsg($this->_languageManager->getText('errorNoJointsUsersInClassSecondRequest'));
			if(!isset($jointsUsersInClass ['request#1'])) {
				$this->_interface->dieError($this->_languageManager->getText('errorNoJointsUsersInClassInAssignUsersToClass'));
			}
		}
		return $jointsUsersInClass;
	}

	private function jointsUsersInClassFirstRequestGetAndThrowWhenVoid () {

		try {
			$status = $this->_usersInClassStatusManager->statusGetByName ('waiting');
			$joints = $this->_jointUsersInClassManager->getAllJointsWithStatusId($status ['ID']);
		} catch (MySQLVoidDataException $e) {
			throw new MySQLVoidDataException('No UsersInClass-Joints with firstRequests');
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointsUsersInClassFirstRequest'));
		}
		return $joints;
	}

	/**
	 * @throws MySQLVoidDataException
	 */
	private function jointsUsersInClassSecondRequestGetAndThrowWhenVoid () {

		try {
			$status = $this->_usersInClassStatusManager->statusGetByName ('request2');
			$joints = $this->_jointUsersInClassManager->getAllJointsWithStatusId($status ['ID']);
		} catch (MySQLVoidDataException $e) {
			throw new MySQLVoidDataException('No UsersInClass-Joints with secondRequests');
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchJointsUsersInClassSecondRequest'));
		}
		return $joints;
	}

	private function menuDisplay () {

		$this->_interface->showAssignUsersToClassMenu();
	}

	private function usersGetAll () {

		try {
			$users = $this->_usersManager->getAllUsers();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoUsers'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorGetUsers'));
		}
		return $users;
	}

	/**
	 * @return all Classes of the Database
	 */
	private function classGetAll () {

		try {
			$classes = $this->_classManager->getAllClasses();
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorNoClasses'));
		} catch (Exception $e) {
			$this->_interface->dieError($this->_languageManager->getText('errorFetchClass'));
		}
		return $classes;
	}

	private function jointsUsersInClassGetByUserId ($userId) {

		echo 'noch nicht fertig';
		try {
			$joints = $this->_jointUsersInClassManager->getAllJointsOfUserId($userId);
		} catch (MySQLVoidDataException $e) {
		} catch (Exception $e) {
		}
		return $joints;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * @var Users[] Users which should be assigned to the Classes
	 */
	private $_users;

	/********************
	 * DatabaseManagers
	********************/
	/**
	 * @var KuwasysUsersManager
	 */
	private $_usersManager;

	/**
	 * @var KuwasysClassManager
	 */
	private $_classManager;

	/**
	 * @var KuwasysJointUsersInClass
	 */
	private $_jointUsersInClassManager;

	/**
	 * @var ClassesInterface
	 */
	private $_interface;

	/**
	 * @var KuwasysLanguageManager
	 */
	private $_languageManager;

	/**
	 * @var KuwasysUsersInClassStatusManager
	 */
	private $_usersInClassStatusManager;
}

?>