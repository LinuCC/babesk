<?php

namespace administrator\System\User\UserUpdateWithSchoolyearChange;

/**
* Represents a conflict
*/
class Conflict
{
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function populateByConflictId($id) {

		if(empty(self::$popstmt)) {
			$this->popstmtSetup();
		}

		self::$popstmt->execute();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function popstmtSetup() {

		self::$popstmt = $this->_pdo->prepare(
			'SELECT tc.ID as conflictId, tc.tempUserId AS userId,
				tc.type AS type,
				IFNULL(u.forename, tu.forename) AS forename,
				IFNULL(u.name, tu.name) AS name,
				CONCAT(g.gradelevel, "-", g.label) AS origGrade,
				CONCAT(tu.gradelevel, "-", tu.label) AS newGrade
			FROM UserUpdateTempConflicts tc
				LEFT JOIN UserUpdateTempUsers tu ON tu.ID = tc.tempUserId
				LEFT JOIN users u ON u.ID = tc.origUserId
				LEFT JOIN usersInGradesAndSchoolyears uigs
					ON u.ID = uigs.userId
					AND uigs.schoolyearId = @activeSchoolyear
				LEFT JOIN Grades g ON uigs.gradeId = g.ID
			WHERE solved = 0
				ORDER BY type LIMIT 10'
		);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	public $conflictId;

	public $origUserId;

	public $tempUserId;

	public $origGradelevel;

	public $origGradelabel;

	public $tempGradelevel;

	public $tempGradelabel;

	public $forename;

	public $name;

	public $type;

	public $solved;

	protected $_popstmt;
}

?>