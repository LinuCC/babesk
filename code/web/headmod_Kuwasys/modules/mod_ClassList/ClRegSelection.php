<?php


require_once PATH_ACCESS_KUWASYS . 'KuwasysDatabaseAccess.php';

/**
 * Represents one selection in the Classlist
 */
class ClRegSelection {
	public function __construct ($classId, $statusName, $unitId) {
		$this->classId = $classId;
		$this->statusName = $statusName;
		$this->unitId = $unitId;
	}

	public $class;
	public $status;
	public $unit;

	/**
	 * Allowing to fetch data from the Database
	 */
	public $classId;
	public $statusName;
	public $unitId;

	public static function classesSet ($selections, $classes) {
		foreach ($selections as &$sel) {
			foreach ($classes as $class) {
				if ($sel->classId == $class ['ID']) {
					$sel->class = $class;
					continue 2;
				}
			}
		}
		return $selections;
	}

	public static function statusSet ($selections, $status) {
		foreach ($selections as &$sel) {
			foreach ($status as $stat) {
				if ($sel->statusName == $stat ['name']) {
					$sel->status = $stat;
					continue 2;
				}
			}
		}
		return $selections;
	}

	public static function unitsSet ($selections, $units) {
		foreach ($selections as &$sel) {
			foreach ($units as $unit) {
				if ($sel->unitId == $unit ['ID']) {
					$sel->unit = $unit;
					continue 2;
				}
			}
		}
		return $selections;
	}

	public static function classesGetBy ($selections, $dbAccMng) {
		$classIds = array ();
		foreach ($selections as $sel) {
			$classIds [] = $sel->class ['ID'];
		}
		return $dbAccMng->dbAccessExec (KuwasysDatabaseAccess::ClassManager,
			'getClassesByClassIdArray', array ($classIds));
	}

	public static function jUserInClassGetByStatus ($selections, $dbAccMng) {
		$jointIds = array ();
		foreach ($selections as $sel) {
			$statusIds [] = $sel->status ['ID'];
		}
		return $dbAccMng->dbAccessExec (KuwasysDatabaseAccess::JUserInClassManager, 'getJointsOfUserWithStatusArray', array ($_SESSION ['uid'], $statusIds));
	}

	public static function unitHasFirstRequest ($selections, $unitId) {
		foreach ($selections as $sel) {
			if ($sel->unit ['ID'] == $unitId) {
				if ($sel->status ['name'] == 'request1') {
					return true;
				}
			}
		}
		return false;
	}

}

?>