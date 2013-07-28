<?php

require_once 'AssignUsersToClassesMoveUser.php';
require_once 'AssignUsersToClassesAddUser.php';

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
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////

	public function execute () {
		if (isset ($_POST ['tempTableResetConfirmed'])) {
			if ($this->tempTableIsExisting ()) {
				$this->tempTableDrop ();
			}
			$this->tempTableCreate ();
			$this->usersAssignToTemp ();
			$link = '<br /><a
			href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses">zurück</a>';
			$this->_interface->dieMsg (
				sprintf('Die Tabelle wurde erfolgreich erstellt. %s', $link));
		}
		else if (isset ($_POST ['tempTableResetNotConfirmed'])
			|| isset ($_GET ['showClasses'])) {
			$this->listShow ();
		}
		else if (isset ($_GET ['showClassDetails'])) {
			$this->classDetailsShow ();
		}
		else if (isset ($_GET ['toDatabase'])) {
			$this->origJointsChange ();
		}
		else if (isset ($_GET ['moveUser'])) {
			AssignUsersToClassesMoveUser::init (
				$this->_interface, self::$tableName);
			AssignUsersToClassesMoveUser::execute ();
		}
		else if (isset ($_GET ['addUser'])) {
			AssignUsersToClassesAddUser::init (
				$this->_interface, self::$tableName);
			AssignUsersToClassesAddUser::execute ();
		}
		else {
			$te = $this->tempTableIsExisting ();
			$this->_interface->showAssignUsersToClassesTempTableCreation ($te);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function tempTableIsExisting () {
		$query = 'show tables like "' . self::$tableName . '";';
		try {
			$blubb = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			return false;
		}
		return true;
	}

	protected function tempTableCreate () {
		$query = '
		CREATE TABLE IF NOT EXISTS `' . self::$tableName . '` (
		`ID` int(11) unsigned NOT NULL auto_increment,
		`UserID` int(11) unsigned NOT NULL,
		`ClassID` int(11) unsigned NOT NULL,
		`statusId` int(11) unsigned NOT NULL,
		Primary Key (`ID`)
		);';
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte die temporäre Tabelle nicht erstellen');
		}
	}

	protected function tempTableDrop () {
		$query = '
		DROP TABLE ' . self::$tableName . '
		';
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			$this->_interface->dieError (
				'Konnte die temporäre Tabelle nicht löschen');

		}
	}

	protected function dataOrigFetch ($statusName) {
		$subQuerySelectStatus = sprintf('(SELECT ID
					FROM usersInClassStatus
					WHERE name="%s")', $statusName);
		$query = '
		SELECT class.ID AS classId, class.label AS classLabel,
			class.maxRegistration AS maxRegistration, class.unitId AS unitId,
			users.ID AS userId, users.forename AS userForename,
			users.name AS userName, jointUsersInClass.ID as id
		FROM jointUsersInClass
			LEFT JOIN class ON jointUsersInClass.ClassID = class.ID
			LEFT JOIN users ON jointUsersInClass.UserID = users.ID
		WHERE
			statusId = ' . $subQuerySelectStatus . '
		;';
		try {
			$data = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			return NULL;
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte die Wunschdaten nicht abrufen');
		}
		return $data;
	}

	protected function usersAssignToTemp () {
		$firstReq = $this->dataOrigFetch ('request1');
		$secondReq = $this->dataOrigFetch ('request2');
		$actives = $this->dataOrigFetch ('active');
		if (!isset($firstReq) && !isset($secondReq)) {
			$this->_interface->dieError ('Es wurden keine Wünsche gefunden');
		}
		$activeCount = $this->activeCountPerClassGet ($actives);
		$sortedReq = $this->requestsResortToClassIds ($firstReq, $secondReq);
		$toAdd = $this->randUserToClassPick ($sortedReq, $activeCount);
		$this->dataAddToTemp ($toAdd);
	}

	protected function activeCountPerClassGet ($active) {

		$counter = array ();
		if (!isset ($active)) {
			return NULL;
		}
		foreach ($active as $row) {
			if (isset ($counter [$row ['classId']])) {
				$counter [$row ['classId']] += 1;
			}
			else {
				$counter [$row ['classId']] = 1;
			}
		}
		return $counter;
	}

	protected function requestsResortToClassIds ($firstReq, $secondReq) {
		$newReq = array ();
		if (count ($firstReq)) {
			foreach ($firstReq as $req) {
				$newReq [$req ['classId']] ['request1'] [] = $req;
				if (!isset ($newReq [$req ['classId']] ['maxRegistration'])) {
					$newReq [$req ['classId']] ['maxRegistration'] = $req ['maxRegistration'];
				}
			}
		}
		if (count ($secondReq)) {
			foreach ($secondReq as $req) {
				$newReq [$req ['classId']] ['request2'] [] = $req;
				// if ($req ['userId'] == '3376') {
				// 	echo 'Wacken<br />';
				// 	var_dump($req);
				// 	echo '<br />';
				// 	var_dump($newReq [$req ['classId']] ['request2']);
				// 	echo '<br />';
				// }
				if (!isset ($newReq [$req ['classId']] ['maxRegistration'])) {
					$newReq [$req ['classId']] ['maxRegistration'] = $req ['maxRegistration'];
				}
			}
		}
		return $newReq;
	}

	protected function randUserToClassPick ($requests, $activeCount) {
		$toAdd = array ();
		$requests = $this->freeSlotsCalc ($requests, $activeCount);
		foreach ($requests as &$classReq) {
			if (!isset ($classReq['request1'])) {
				continue; //only secondary requests
			}
			//add the primary requests
			if (count ($classReq['request1']) <= $classReq ['freeSlots']) {
				foreach ($classReq ['request1'] as $req) {
					$toAdd [] = RToAdd::rowAdd ($req, RToAdd::sActive,
						$classReq ['freeSlots']);
				}
			}
			else {
				//Randomize who joins the class
				$shuffled = $classReq ['request1'];
				shuffle ($shuffled);
				$counter = 0;
				while ($classReq ['freeSlots'] > 0) {
					$req = $shuffled [$counter];
					$toAdd [] = RToAdd::rowAdd ($req, RToAdd::sActive,
						$classReq ['freeSlots']);
					$counter ++;
				}
				//Add the leftover users to the waiting-list
				for (;$counter < count ($shuffled); $counter ++) {
					$req = $shuffled [$counter];
					$toAdd [] = new RToAdd($req, RToAdd::sWaiting);
				}
			}
		}
		//now add the secondary requests
		foreach ($requests as &$classReq) {

			if (!isset ($classReq['request2'])) {
				continue; //no secondary requests to handle
			}
			if (count ($classReq['request2']) <= $classReq ['freeSlots']) {
				//Add all because for everyone is a free Slot available
				foreach ($classReq ['request2'] as $req) {
					$status = $this->getStatusByUserAndUnit ($toAdd,
						$req ['userId'], $req ['unitId']);
					if ($status == RToAdd::sActive) {
						$toAdd [] = new RToAdd ($req, RToAdd::sNotAssigned);
						//user has already been added at this unit (aka weekday)
						continue;
					}
					else if ($status == RToAdd::sWaiting) {
						$deleted = RToAdd::rowDelete ($toAdd, $req ['userId'],
							$req ['unitId']);
						$toAdd [] = new RToAdd ($deleted, RToAdd::sNotAssigned);
					}
					$toAdd [] = new RToAdd ($req, RToAdd::sActive);
					$classReq ['freeSlots'] --;
				}
			}
			else {
				//Randomize who joins the class
				$shuffled = $classReq ['request2'];
				shuffle ($shuffled);
				$counter = 0;
				while ($classReq ['freeSlots'] > 0 && isset ($shuffled [$counter])) {
					//add user as active
					$req = $shuffled [$counter];
					$alreadyAddedStatus = $this->getStatusByUserAndUnit ($toAdd,
						$req ['userId'], $req ['unitId']);
					if ($alreadyAddedStatus == RToAdd::sActive) {
						$toAdd [] = new RToAdd ($req, RToAdd::sNotAssigned);
						//user has already been added at this unit (aka weekday)
						$counter ++;
						continue;
					}
					else if ($alreadyAddedStatus == RToAdd::sWaiting) {
						$deleted = RToAdd::rowDelete ($toAdd, $req ['userId'],
							$req ['unitId']);
						$toAdd [] = new RToAdd ($deleted, RToAdd::sNotAssigned);
					}
					$toAdd [] = RToAdd::rowAdd ($req, RToAdd::sActive,
						$classReq ['freeSlots']);
					$counter ++;
				}
				//Add the leftover users to the waiting-list
				for (;$counter < count ($shuffled); $counter ++) {
					$req = $shuffled [$counter];
					$alreadyAddedStatus = $this->getStatusByUserAndUnit ($toAdd,
						$req ['userId'], $req ['unitId']);
					if ($alreadyAddedStatus) {
						//user has already been added at this unit (aka weekday), either as active or waiting (we dont want two waitings)
						continue;
					}
					$toAdd [] = new RToAdd ($req, RToAdd::sWaiting);
				}
			}
		}
		return $toAdd;
	}

	protected function getStatusByUserAndUnit ($toAdd, $userId, $unitId) {
		foreach ($toAdd as $row) {
			if ($row->getUserId () == $userId) {
				if ($row->getUnitId () == $unitId) {
					return $row->getStatus ();
				}
			}
		}
		return false;
	}

	protected function freeSlotsCalc ($requests, $activeCount) {
		//calculate the free Slots available
		foreach ($requests as $classId => &$classReq) {
			if (!isset ($activeCount [$classId])) {
				$activeCount [$classId] = 0;
			}
			$classReq ['freeSlots'] = $classReq ['maxRegistration'] - $activeCount [$classId];
		}
		return $requests;
	}

	protected function dataAddToTemp ($data) {
		$valueQuery = '';
		$aq = 'SELECT ID FROM usersInClassStatus WHERE name="active"';
		$wq = 'SELECT ID FROM usersInClassStatus WHERE name="waiting"';
		$activeStatusId = TableMng::query($aq, true);
		$waitingStatusId = TableMng::query ($wq, true);
		foreach ($data as $row) {
			if ($row->getStatus () == RToAdd::sActive) {
				$statusId = $activeStatusId [0] ['ID'];
			}
			else if ($row->getStatus () == RToAdd::sWaiting){
				$statusId = $waitingStatusId [0] ['ID'];
			}
			else if ($row->getStatus () == RToAdd::sNotAssigned) {
				$statusId = '0';//indicating that the original row should be deleted
			}
			$valueQuery .= sprintf ('(%s, %s, %s), ', $row->getUserId (), $row->getClassId (), $statusId);
		}
		$valueQuery = rtrim ($valueQuery, ', ');
		$query = sprintf ('INSERT INTO %s (UserID, ClassID, statusId) VALUES %s',
			self::$tableName, $valueQuery);
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte die temporären Einträge nicht hinzufügen! ' . $e->getMessage ());
		}
	}

	protected function listShow () {
		$classes = $this->classesAsListGet ();
		$this->_interface->showAssignUsersToClassesClassList ($classes);
	}

	protected function classesAsListGet () {
				$subQuerySelectStatus = '
		(SELECT ID
			FROM usersInClassStatus
			WHERE name="%s"
			)
		';
		$query = '
		SELECT unit.ID AS unitId, unit.translatedName AS unitName,
			class.ID AS classId, class.label AS classLabel,
			(SELECT COUNT(*)
				FROM ' . self::$tableName . ' jt
				WHERE jt.ClassID = class.ID AND
				jt.statusId = (SELECT ID FROM usersInClassStatus WHERE
					name="active")) AS activeCount
		FROM ' . self::$tableName . ' t
			JOIN class ON t.ClassID = class.ID
			JOIN kuwasysClassUnit unit ON class.unitId = unit.ID
			GROUP BY class.ID
		;';
		try {
			$results = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError ('Keine Kursänderungen vorhanden die angezeigt werden könnten');
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte die Änderungen nicht abrufen!');
		}
		return $results;
	}

	protected function resortToClassIds () {
		$resorted = array ();
		foreach ($requests as $req) {
			$resorted [$req ['classId']] [] = $req;
			if (!isset ($resorted [$req ['classId']] ['maxRegistration'])) {
				$resorted [$req ['classId']] ['maxRegistration'] = $req ['maxRegistration'];
			}
		}
		return $resorted;
	}

	protected function classDetailsShow () {
		$classId = $_GET ['showClassDetails'];
		if (isset ($_GET ['toStatus'])) {
			$this->jTempChange ($_GET ['id'], $_GET ['toStatus']);
		}
		$dataPrimary = $this->dataChangedFetch ('active', $classId);
		$dataSecondary = $this->dataChangedFetch ('waiting', $classId);
		$dataRemoved = $this->dataChangedFetch ('0', $classId, true);
		$classname = $this->getClassnameByClassId ($classId);
		if (!$dataPrimary && !$dataSecondary && !$dataRemoved) {
			$this->_interface->dieError ('Keine Daten gefunden');
		}
		$this->_interface->showAssignUsersToClassesUserList (
			$classname, $dataPrimary, $dataSecondary, $dataRemoved);
	}

	protected function dataChangedFetch ($statusName, $classId,
		$isStatusNameActuallyAStatusIdBecauseIAmDumb = false) {
		if (!$isStatusNameActuallyAStatusIdBecauseIAmDumb) {
			$subQuerySelectStatus = sprintf ('
					(SELECT ID FROM usersInClassStatus WHERE name="%s")',
					$statusName);
		}
		else {
			$subQuerySelectStatus = $statusName;
		}
		$query = 'SELECT t.ID as id, c.label AS classLabel, c.ID AS classId,
			cu.translatedName AS unitName,
			CONCAT(u.forename, " ", u.name) AS username, u.ID AS userId,
			u.telephone AS userTelephone,
			cs.name AS statusName, ucs.translatedName AS origStatusName,
			CONCAT(g.gradeValue, g.label) as grade
		FROM ' . self::$tableName . ' t
			LEFT JOIN users u ON u.ID = t.UserID
			LEFT JOIN jointUsersInGrade uig ON u.ID = uig.UserID
			LEFT JOIN grade g ON g.ID = uig.GradeID
			LEFT JOIN class c ON c.ID = t.ClassID
			LEFT JOIN kuwasysClassUnit cu ON cu.ID = c.unitId
			LEFT JOIN usersInClassStatus cs ON t.statusId = cs.ID
			LEFT JOIN jointUsersInClass uic ON t.ClassID = uic.ClassID AND
				t.UserID = uic.UserID
			LEFT JOIN usersInClassStatus ucs ON uic.statusId = ucs.ID
		WHERE t.ClassID = "' . $classId . '" AND
			t.statusId = ' . $subQuerySelectStatus . '
		ORDER BY grade
		;';
		try {
			$data = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			return false;
		} catch (Exception $e) {
			$this->_interface->dieError (
				'Konnte die zu verändernden Daten nicht abrufen');
		}
		return $data;
	}

	protected function jTempChange ($id, $statusName) {
		switch ($statusName) {
			case 'active':
				$subQ = sprintf ('
					(SELECT ID FROM usersInClassStatus WHERE name="%s")', 'active');
				break;
			case 'waiting':
				$subQ = sprintf ('
					(SELECT ID FROM usersInClassStatus WHERE name="%s")', 'waiting');
				break;
			case 'removed':
				$subQ = sprintf ('%s', '0'); //set ID to zero
				break;
		}
		$query = 'UPDATE ' . self::$tableName . ' SET statusId = ' . $subQ .
		' WHERE ID = ' . $id . ' ;';
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			$this->_interface->showError ('Konnte den Status des Schülers nicht verändern! ' . $e->getMessage ());
		}
	}

	protected function getClassnameByClassId ($classId) {
		$query = 'SELECT label FROM class WHERE ID = ' . $classId . ';';
		try {
			$class = TableMng::query ($query);
		} catch (Exception $e) {
			$this->_interface->showError ('Konnte den Klassennamen nicht finden');
		}
		return $class [0] ['label'];
	}

	protected function origJointsChange () {
		$query = 'SELECT * FROM ' . self::$tableName .';';
		try {
			$jointsAll = TableMng::query ($query);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError ('Es gibt keine Veränderungen');
		} catch (Exception $e) {
			$this->_interface->dieError ('Die Veränderungen konnten nicht abgerufen werden');
		}
		$toDelete = array ();
		$toUpdate = array ();
		foreach ($jointsAll as $j) {
			if ($j ['statusId'] != '0') {
				$toUpdate [] = $j;
			}
			else {
				$toDelete [] = $j;
			}
		}
		$this->origJointsDelete ($toDelete);
		$this->origJointsUpdate ($toUpdate);
		$this->_interface->dieMsg ('Die Schüler wurden erfolgreich zugewiesen');
	}

	protected function origJointsDelete ($joints) {
		$whereQuery = '';
		if (!count ($joints)) {
			return;
		}
		foreach ($joints as $j) {
			$whereQuery .= sprintf (' ClassID = "%s" AND UserID = "%s" OR', $j ['ClassID'], $j ['UserID']);
		}
		$whereQuery = rtrim ($whereQuery, 'OR'); //remove last OR
		$query = sprintf ('DELETE FROM jointUsersInClass WHERE %s', $whereQuery);
		try {
			TableMng::query ($query);
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte nicht alle überflüssige Joints löschen!' . $e->getMessage ());
		}
	}

	protected function origJointsUpdate ($joints) {
		$query = '';
		foreach ($joints as $j) {
			$query .= sprintf ('UPDATE jointUsersInClass SET statusId = "%s" WHERE ClassID = "%s" AND UserID = "%s";', $j ['statusId'], $j ['ClassID'], $j ['UserID']);
		}
		try {
			TableMng::queryMultiple ($query );
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte die originalen Joints nicht vollständig verändern. Möglicherweise sind jetzt korrupte Daten vorhanden');
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * @var Users[] Users which should be assigned to the Classes
	 */
	private $_users;

	private $_interface;

	private $_languageManager;

	protected static $tableName = 'assignUsersToClassesTemp';

}

class RToAdd {
	public function __construct ($row, $status) {
		$this->row = $row;
		$this->status = $status;
	}

	/**
	 * Returns the Variable $row ['unitId']
	 * @return $row ['unitId']
	 */
	public function getUnitId () {
		return $this->row ['unitId'];
	}

	/**
	 * Returns the Variable $row ['userId']
	 * @return $row ['userId']
	 */
	public function getUserId () {
		return $this->row ['userId'];
	}

	/**
	 * Returns the Variable $row ['classId']
	 * @return $row ['classId']
	 */
	public function getClassId () {
		return $this->row ['classId'];
	}

	/**
	 * Returns the Variable $status
	 * @return $status
	 */
	public function getStatus () {
		return $this->status;
	}

	/**
	 * Creates a new RToAdd-Object and sets the given Counter back by one
	 */
	public static function rowAdd ($row, $status, &$counter) {
		$obj = new RToAdd ($row, $status);
		$counter --;
		return $obj;
	}

	public static function rowDelete (&$rows, $userId, $unitId) {
		$rowDeleted = false;
		foreach ($rows as $row) {
			if ($row->getUserId () == $userId) {
				if ($row->getUnitId () == $unitId) {
					$rowDeleted = $row;
					unset ($row);
				}
			}
		}
		return $rowDeleted;
	}

	const sWaiting = 1;
	const sActive = 2;
	const sNotAssigned = 3;

	protected $row;
	protected $status;

}

?>
