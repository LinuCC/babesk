<?php

namespace administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses;

require_once __DIR__ . '/AssignUsersToClasses.php';

/**
 * Creates the new Assignments from the data and deletes the old, if exists
 */
class Reset extends \administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);

		if($this->tableExists()) {
			$this->tableDrop();
		}
		$this->tableCreate();
		$this->statusSet();
		$this->tableFill();
		$this->_smarty->assign('backlink', 'javascript:history.back()');
		$this->_interface->dieSuccess(_g('The Data was successfully Assigned. You can now go back and view and edit the temporary changes'));
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Drops the UsersToClasses-Table
	 *
	 * Dies displaying a Message when the Query could not be executed
	 */
	private function tableCreate() {

		try {
			$this->_pdo->exec('CREATE TABLE IF NOT EXISTS
				`KuwasysTemporaryRequestsAssign` (
					`userId` int(11) unsigned NOT NULL,
					`classId` int(11) unsigned NOT NULL,
					`statusId` int(11) unsigned NOT NULL,
					`origUserId` int(11) unsigned NOT NULL,
					`origClassId` int(11) unsigned NOT NULL,
					`origStatusId` int(11) unsigned NOT NULL,
					PRIMARY KEY(`userId`, `classId`)
				);');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not create the UsersToClasses-Table!'));
		}
	}

	/**
	 * Drops the UsersToClasses-Table
	 *
	 * Dies displaying a Message when the Query could not be executed
	 */
	private function tableDrop() {

		try {
			$this->_pdo->exec('DROP TABLE KuwasysTemporaryRequestsAssign');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not delete the UsersToClasses-Table!'));
		}
	}

	private function tableFill() {

		$requestsSorted = $this->requestsSort($this->requestsGet());
		$this->requestsAssign($requestsSorted);
		$this->upload();
	}

	/**
	 * Fetches and caches the statusnames to statusid assignments
	 */
	private function statusSet() {

		try {
			$res = $this->_pdo->query(
				'SELECT ID, name FROM usersInClassStatus WHERE 1'
			);

			$this->_status = $res->fetchAll(\PDO::FETCH_KEY_PAIR);
			$this->_status[0] = 'removed';

		} catch (\PDOException $e) {
			$this->_interface->dieError(_g('Could not fetch the status'));
		}
	}

	/**
	 * Fetches and returns the requests from the database
	 * @return array "<index>" => ["statusId" => "<id">, ...]
	 */
	private function requestsGet() {

		try {
			$res = $this->_pdo->query(
				'SELECT uic.statusId AS statusId, uic.ClassID AS classId,
					uic.UserID AS userId, c.maxRegistration AS maxRegistration,
					c.unitId AS unitId
				FROM jointUsersInClass uic
				JOIN KuwasysClasses c ON uic.ClassID = c.ID
				WHERE c.schoolyearId = @activeSchoolyear
					AND (
						uic.statusId = (
							SELECT ID FROM usersInClassStatus
								WHERE name="request1"
						) OR
						uic.statusId = (
							SELECT ID FROM usersInClassStatus
								WHERE name="request2"
						)
					)
				ORDER BY uic.statusId
			');

			return $res->fetchAll(\PDO::FETCH_ASSOC);

		} catch (\PDOException $e) {
			$res->closeCursor();
			$this->_logger->log('Error fetching the requests',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the requests!'));
		}
	}

	/**
	 * Sorts the requests, allowing to use them later on
	 * @param  array  $requests raw request-data fetched from the database
	 * @return array            sorted request-data:
	 *         "<statusId>" => [
	 *             "<classId>" => [
	 *                 "maxRegistration" => "<max Registrations of class>",
	 *                 "unitId" => "<unit of class>",
	 *                 "users" => [
	 *                     "<index>" => "<userId>"
	 *                 ]
	 *             ]
	 *         ]
	 */
	private function requestsSort($requests) {

		foreach($requests as $request) {
			$sorted[(int)$request['statusId']][(int)$request['classId']]
				['users'][] = (int)$request['userId'];
			$sorted[(int)$request['statusId']][(int)$request['classId']]
				['maxRegistration'] = (int)$request['maxRegistration'];
			$sorted[(int)$request['statusId']][(int)$request['classId']]
				['unitId'] = (int)$request['unitId'];
		}

		return $sorted;
	}

	/**
	 * Creates assignments of users to classes by requests
	 * Currently depends on the order of the ids of the status; a lower
	 * statusId will be more important
	 * @param  array  $sortedRequests sorted request-data:
	 *         "<statusId>" => [
	 *             "<classId>" => [
	 *                 "maxRegistration" => "<max Registrations of class>",
	 *                 "unitId" => "<unit of class>",
	 *                 "users" => [
	 *                     "<index>" => "<userId>"
	 *                 ]
	 *             ]
	 *         ]
	 */
	private function requestsAssign($sortedRequests) {

		foreach($sortedRequests as $statusId => $reqByStatus) {
			foreach($reqByStatus as $classId => $reqByClass) {
				if($reqByClass['maxRegistration'] <
					count($reqByClass['users'])
				) {
					//shuffle for randomness who gets assigned to a class if
					//more user request it than allowed
					shuffle($reqByClass['users']);
				}
				$this->assign(
					$statusId, $reqByClass['maxRegistration'], $classId,
					$reqByClass['unitId'], $reqByClass['users']
				);
			}
		}
	}

	/**
	 * Assigns users to classes
	 */
	private function assign(
		$status, $maxRegistration, $class, $classUnit, $requests
	) {

		if(!isset($this->_classCount[$class])) {
			$this->_classCount[$class] = 0;
		}

		foreach($requests as $userId) {

			if(isset($this->_toadd[$userId][$classUnit])) {
				//An assignment already exists on this classUnit for this
				//user, check its status
				$activeClassId = $this->classesIncludeStatus(
					$this->_toadd[$userId][$classUnit], 'active'
				);
				$waitingClassId = $this->classesIncludeStatus(
					$this->_toadd[$userId][$classUnit], 'waiting'
				);
				if($this->_classCount[$class] >= $maxRegistration) {
					//class is full, add user as waiting or remove if he found
					//something better already
					$newStatus = ($activeClassId !== false) ? 0 :
						array_search('waiting', $this->_status);
					$this->_toadd[$userId][$classUnit][$class]['newStatus'] =
						$newStatus;
					$this->_toadd[$userId][$classUnit][$class]['origStatus'] =
						$status;
				}
				else if($activeClassId !== false) {
					//user already got another class on this day, he does
					//not need another one
					$this->_toadd[$userId][$classUnit][$class]
						['newStatus'] = 0;
					$this->_toadd[$userId][$classUnit][$class]
						['origStatus'] = $status;
				}
				else if($waitingClassId !== false) {
					//user already waits for one class
					$this->waitingEntriesRemove($userId, $classUnit);
					$this->_toadd[$userId][$classUnit][$class]['newStatus'] =
						array_search('active', $this->_status);
					$this->_toadd[$userId][$classUnit][$class]['origStatus'] =
						$status;
					$this->classCountIncrement($class);
				}
				else {
					//user has no assignments in this classunit yet
					$this->_toadd[$userId][$classUnit][$class]['newStatus'] =
						array_search('active', $this->_status);
					$this->_toadd[$userId][$classUnit][$class]['origStatus'] =
						$status;
				}
			}
			else {
				//No assignment exists for this user and classunit yet
				if($this->_classCount[$class] >= $maxRegistration) {
					//class is full, add user as waiting or remove if he found
					//something better already
					$this->_toadd[$userId][$classUnit][$class]['newStatus'] =
						array_search('waiting', $this->_status);
					$this->_toadd[$userId][$classUnit][$class]['origStatus'] =
						$status;
				}
				else {
					$this->_toadd[$userId][$classUnit][$class]['newStatus'] =
						array_search('active', $this->_status);
					$this->_toadd[$userId][$classUnit][$class]['origStatus'] =
						$status;
					$this->classCountIncrement($class);
				}
			}
		}
	}

	/**
	 * remove all already added waiting entries of user at day
	 */
	private function waitingEntriesRemove($userId, $unitId) {

		while(
			($waitingClassId = $this->classesIncludeStatus(
				$this->_toadd[$userId][$unitId], 'waiting')) !== false
		) {
			$this->_toadd[$userId][$unitId][$waitingClassId]['newStatus'] = 0;
		}
	}

	/**
	 * Increments the classCount by one (and creates an entry if not exists)
	 * @param  int    $classId The Id of the class to increment the count
	 */
	private function classCountIncrement($classId) {
		if(!isset($this->_classCount[$classId])) {
			$this->_classCount[$classId] = 1;
		}
		else {
			$this->_classCount[$classId] += 1;
		}
	}

	/**
	 * Checks if one of the given assignments has a status with the name
	 * @param  array  $classesWithStatus the status of the class-assignments:
	 *              "<classId>" => "<statusId>"
	 * @param  $statusName The name of the status to check for
	 * @return int                       The classId if one of the classes has
	 *                                   a status with this name status, else
	 *                                   false
	 */
	private function classesIncludeStatus($classesWithStatus, $statusName) {

		if(!count($classesWithStatus)) {
			return false;
		}

		foreach($classesWithStatus as $classId => $statusData) {
			if($this->_status[$statusData['newStatus']] == $statusName) {
				return $classId;
			}
		}

		return false;
	}

	private function upload() {

		try {
			$stmt = $this->_pdo->prepare(
				'INSERT INTO KuwasysTemporaryRequestsAssign
					(`userId`, `classId`, `statusId`, `origUserId`,
					`origClassId`, `origStatusId`)
				VALUES
					(:userId, :classId, :statusId, :userId, :classId,
						:origStatusId);'
			);

			foreach($this->_toadd as $userId => $units) {
				foreach($units as $unitId => $classes) {
					foreach($classes as $classId => $statusData) {
						$stmt->execute(array(
							'userId' => $userId,
							'classId' => $classId,
							'statusId' => $statusData['newStatus'],
							'origStatusId' => $statusData['origStatus']
						));
					}
				}
			}

		} catch (\PDOException $e) {
			$this->_logger->log('Error uploading the data',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not upload the data!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The Requests to add to the temporary table
	 * @var array  "<userId>" => ["<unitId>" => ["<classId>" =>
	 *            ["<statusData>" => ["newStatus" => "<newStatusId>",
	 *             "origStatus" => "<originalStatusId>"
	 *            ]]]]
	 */
	private $_toadd;

	/**
	 * data of the status
	 * @var array "<statusId>" => "<statusName>"
	 */
	private $_status;

	/**
	 * Counts the assigned active users per class
	 * @var array "<classId>" => "<usercount>"
	 */
	private $_classCount;
}

?>