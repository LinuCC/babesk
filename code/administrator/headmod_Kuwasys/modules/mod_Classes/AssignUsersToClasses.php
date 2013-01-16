<?php

require_once PATH_INCLUDE . '/sqlAccess/TableMng.php';

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
		TableMng::init ();
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
			$blubb = TableMng::query ($query, true);
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
			$this->interface->dieError ('Konnte die temporäre Tabelle nicht erstellen');
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
		$subQuerySelectStatus = '
		(SELECT ID
			FROM usersInClassStatus
			WHERE name="%s"
			)
		';
		$query = '
		SELECT class.ID AS classId, class.label AS classLabel,
			class.maxRegistration AS maxRegistration, class.unitId AS unitId,
			users.ID AS userId, users.forename AS userForename,
			users.name AS userName
		FROM jointUsersInClass
			LEFT JOIN class ON jointUsersInClass.ClassID = class.ID
			LEFT JOIN users ON jointUsersInClass.UserID = users.ID
		WHERE
			statusId = ' . sprintf($subQuerySelectStatus, $statusName) . '
		';
		try {
			$data = TableMng::query ($query, true);
		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError ('Konnte keine Wünsche von Benutzern finden');
		} catch (Exception $e) {
			$this->_interface->dieError ('Konnte die Wunschdaten nicht abrufen');
		}
		return $data;
	}

	protected function usersAssignToTemp () {
		$firstReq = $this->dataOrigFetch ('request1');
		$secondReq = $this->dataOrigFetch ('request2');
		$activeCount = $this->activeCountPerClassGet (
			$this->dataOrigFetch ('active'));
		$sortedReq = $this->requestsResortToClassIds ($firstReq, $secondReq);
		$toAdd = $this->randUserToClassPick ($sortedReq, $activeCount);
		$this->dataAddToTemp ($toAdd);
	}

	protected function activeCountPerClassGet ($active) {
		$counter = array ();
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
		foreach ($firstReq as $req) {
			$newReq [$req ['classId']] ['request1'] [] = $req;
			if (!isset ($newReq [$req ['classId']] ['maxRegistration'])) {
				$newReq [$req ['classId']] ['maxRegistration'] = $req ['maxRegistration'];
			}
		}
		foreach ($secondReq as $req) {
			$newReq [$req ['classId']] ['request2'] [] = $req;
			if (!isset ($newReq [$req ['classId']] ['maxRegistration'])) {
				$newReq [$req ['classId']] ['maxRegistration'] = $req ['maxRegistration'];
			}
		}
		return $newReq;
	}

	protected function randUserToClassPick ($requests, $activeCount) {
		$toAdd = array ();
		$usersAlreadyAdded = array ();
		$requests = $this->freeSlotsCalc ($requests, $activeCount);
		foreach ($requests as $classReq) {
			if (!isset ($classReq['request1'])) {
				continue; //only secondary requests
			}
			//add the primary requests
			if (count ($classReq['request1']) <= $classReq ['freeSlots']) {
				foreach ($classReq ['request1'] as $req) {
					$toAdd [] = $req;
					$classReq ['freeSlots'] --;
					$usersAlreadyAdded [$req ['userId']] [$req ['unitId']] = true;
				}
			}
			else {
				//Randomize who joins the class
				$shuffled = $classReq ['request1'];
				shuffle ($shuffled);
				$counter = 0;
				while ($classReq ['freeSlots'] > 0) {
					$req = $shuffled [$counter];
					$toAdd [] = $req;
					$classReq ['freeSlots'] --;
					$counter ++;
					$usersAlreadyAdded [$req ['userId']] [$req ['unitId']] = true;
				}
			}
		}
		//now add the secondary requests
		foreach ($requests as $classReq) {
			if (!isset ($classReq['request2'])) {
				continue; //no secondary requests to handle
			}
			if (count ($classReq['request2']) <= $classReq ['freeSlots']) {
				foreach ($classReq ['request2'] as $req) {
					if ($usersAlreadyAdded [$req ['userId']] [$req ['unitId']]) {
						//user has already been added at this unit (aka weekday)
						continue;
					}
					$toAdd [] = $req;
					$classReq ['freeSlots'] --;
					$usersAlreadyAdded [$req ['userId']] [$req ['unitId']] = true;
				}
			}
			else {
				//Randomize who joins the class
				$shuffled = $classReq ['request2'];
				shuffle ($shuffled);
				$counter = 0;
				while ($classReq ['freeSlots'] > 0) {
					$req = $shuffled [$counter];
					if ($usersAlreadyAdded [$req ['userId']] [$req ['unitId']]) {
						//user has already been added at this unit (aka weekday)
						continue;
					}
					$toAdd [] = $req;
					$classReq ['freeSlots'] --;
					$counter ++;
					$usersAlreadyAdded [$req ['userId']] [$req ['unitId']] = true;
				}
			}
		}
		return $toAdd;
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
		$activeStatusId = TableMng::query($aq, true);
		foreach ($data as $row) {
			$valueQuery .= sprintf ('(%s, %s, %s), ', $row ['userId'], $row ['classId'], $activeStatusId [0] ['ID']);
		}
		$valueQuery = rtrim ($valueQuery, ', ');
		$query = sprintf ('INSERT INTO %s (UserID, ClassID, StatusID) VALUES %s',
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
			$results = TableMng::query ($query, true);
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
		$query = 'SELECT t.ID as id, c.label AS classLabel, c.ID as classId,
			cu.translatedName AS unitName,
			CONCAT(u.forename, " ", u.name) AS username,
			cs.name AS statusName
		FROM ' . self::$tableName . ' t
			LEFT JOIN users u ON u.ID = t.UserID
			LEFT JOIN jointUsersInGrade uig ON u.ID = uig.UserID
			LEFT JOIN grade g ON g.ID = uig.GradeID
			LEFT JOIN class c ON c.ID = t.ClassID
			LEFT JOIN kuwasysClassUnit cu ON cu.ID = c.unitId
			LEFT JOIN usersInClassStatus cs ON t.statusId = cs.ID
		WHERE t.ClassID = "' . $classId . '" AND
			t.statusId = ' . $subQuerySelectStatus . '
		;';
		try {
			$data = TableMng::query ($query, true);
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
			$class = TableMng::query ($query, true);
		} catch (Exception $e) {
			$this->_interface->showError ('Konnte den Klassennamen nicht finden');
		}
		return $class [0] ['label'];
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

?>