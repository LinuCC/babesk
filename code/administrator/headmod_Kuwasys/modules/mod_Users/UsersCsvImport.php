<?php

require_once PATH_INCLUDE . '/CsvImporter.php';

class UsersCsvImport {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////
	public static function classInit ($interface, $databaseAccessManager) {
		self::$_interface = $interface;
		self::$_dbMng = $databaseAccessManager;
	}

	public static function import ($filePath, $delimiter) {
		$csvMng = new CsvImporter ($filePath, $delimiter);
		$rows = $csvMng->getContents ();
		$rows = self::varControl ($rows);
		self::dataToDb ($rows);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////
	protected static function varControl ($rows) {
		foreach ($rows as &$row) {
			foreach (self::$_csvStructure as $key) {
				$row = self::keyCheck ($key, $row);
			}
		}
		return $rows;
	}

	protected static function keyCheck ($varName, $rowArray) {
		if (!isset($rowArray[$varName])) {
			$rowArray[$varName] = '';
		}
		return $rowArray;
	}

	protected static function dataToDb ($rows) {
		try {
			$rows = self::gradesHandle ($rows);
			$rows = self::userToDb ($rows);
			self::jUserInGradeToDb ($rows);
		} catch (Exception $e) {
			self::$_interface->dieError ('Konnte die Benutzer nicht importieren. Möglicherweise ist die Datenbank nun beschädigt! Fehler:' . $e->getMessage());
		}
	}

	/**
	 * Adds the new Users to the Db and adds the element _userId to the $rows
	 */
	protected static function userToDb ($rows) {
		$toAdd = array ();
		$schoolyear = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::SchoolyearManager, 'getActiveSchoolyear');
		$userId = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::UserManager,
			'getNextAIUserId', array());
		foreach ($rows as &$row) {
			$process = new DbAMRow ();
			foreach (self::$_csvStructure as $key) {
				if ($key == self::$_csvStructure ['GradeName']) {
					// do Nothing
				}
				else if ($key == self::$_csvStructure ['Birthday']) {
					$timestamp = strtotime ($row [self::$_csvStructure ['Birthday']]);
					$parsed = date ('Y-m-d', $timestamp);
					echo "<br>DATE:";
					var_dump($parsed);
					echo "<br>";
					$process->processFieldAdd ($key, $parsed);
				}
				else {
					$process->processFieldAdd ($key, $row [$key]);
				}
			}
			$toAdd [] = $process;
			$row ['_userId'] = $userId;
			$userId ++;
		}
		self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::UserManager,
			'addMultipleUser', array ($toAdd), 'addMultipleUser');
		self::jUserSchoolyearAdd ($rows);
		return $rows;
	}

	protected static function jUserInGradeToDb ($rows) {
		$toAdd = array ();
		foreach ($rows as $row) {
			if ($row ['_gradeId']) {
				$process = new DbAMRow ();
				$process->processFieldAdd ('UserID', $row ['_userId']);
				$process->processFieldAdd ('GradeID', $row ['_gradeId']);
				$toAdd [] = $process;
			}
			else {
				$this->_interface->showError (sprintf ('Konnte den Nutzer mit der ID "%s" nicht zu seiner Klasse hinzufügen'));
			}
		}
		self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::JUserInGradeManager,
			'addMultipleJoints', array ($toAdd));
	}

	protected static function gradesHandle ($rows) {
		$toSearch = array ();
		$toComp = array ();
		foreach ($rows as &$row) {
			$rowGrade = $row [self::$_csvStructure ['GradeName']];
			if ($rowGrade == '') {
				continue;
			}
			$grade = self::gradeSplit($rowGrade);
			$searchRow = new DbAMRow ();
			$searchRow->searchFieldAdd ('gradeValue', $grade [0]);
			$searchRow->searchFieldAdd ('label', $grade [1]);
			$toSearch [] = $searchRow;
			$toComp [] = $grade;
		}
		if (count ($toSearch)) {
			$grades = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::GradeManager, 'getMultipleGrades', array($toSearch));
			$rows = self::nonExGradeAdd ($grades, $toComp, $rows);
		}
		return $rows;
	}

	/**
	 * Checks for non-existend Grades and adds them to the db
	 */
	protected static function nonExGradeAdd ($grades, $compGrades, $rows) {
		$toAdd = array (); //grades to add to db
		$nextDbGradeId = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::GradeManager, 'getNextAIGradeId');
		foreach ($compGrades as $gradeComp) {
			if (isset ($grades)) {
				foreach ($grades as $grade) {
					$wholeName = $grade ['gradeValue'] . $grade ['label'];
					if ($wholeName == $gradeComp [0] . $gradeComp [1]) {
						$rows = self::gradeIdAdd ($grade, $rows);
						continue 2;
					}
				}
			}
			self::jGradeSchoolyearAdd ($nextDbGradeId);
			$toAdd [] = new DbAMRow (array(
				array(DbAMRow::$ProcessField, new DbAMField('gradeValue', $gradeComp [0])),
				array(DbAMRow::$ProcessField, new DbAMField('label', $gradeComp [1]))));
			foreach ($rows as &$row) {//add GradeID to row
				if ($row ['grade'] == $gradeComp [0] . $gradeComp [1]) {
					$row ['_gradeId'] = $nextDbGradeId;
					continue;
				}
				else {
					$row ['_gradeId'] = false;
				}
			}
			$nextDbGradeId ++;
		}
		if (count ($toAdd)) {
			self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::GradeManager,
				'addMultipleGrades', array($toAdd));
		}
		return $rows;
	}

	protected static function jGradeSchoolyearAdd ($gradeId) {
		$schoolyear = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::SchoolyearManager, 'getActiveSchoolyear');
		self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::JGradeInSchoolyearManager, 'addJoint',
			array ($gradeId, $schoolyear ['ID']));
	}

	protected static function jUserSchoolyearAdd ($rows) {
		$schoolyear = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::SchoolyearManager, 'getActiveSchoolyear');
		$proc = array ();
		foreach ($rows as $row) {
			$process = new DbAMRow ();
			$process->processFieldAdd('UserID', $row ['_userId']);
			$process->processFieldAdd('SchoolYearID', $schoolyear ['ID']);
			$proc [] = $process;
		}
		self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::JUserInSchoolyearManager, 'addMultipleJoints', array ($proc));
	}

	protected static function gradeIdAdd ($grade, $rows) {
		foreach ($rows as &$row) {//add GradeID to row
			if ($row ['grade'] == $grade ['gradeValue'] . $grade ['label']) {
				$row ['_gradeId'] = $grade ['ID'];
			}
		}
		return $rows;
	}

	protected static function gradeSplit ($gradeName) {
		$elements = preg_split('/([0-9][a-zA-Zäöü])/', $gradeName, 2,
			PREG_SPLIT_DELIM_CAPTURE);
		if (count ($elements) < 2) {
			throw new Exception ('Could not split grade "' . $gradeName . '"');
		}
		$elements [1] .= $elements [2];
		unset ($elements [2]);
		$elements [0] .= $elements [1] [0];
		$elements [1] = substr($elements [1], 1);
		return $elements;
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_interface;
	protected static $_dbMng;
	protected static $_csvStructure = array (
		'Forename' => 'forename',
		'Name' => 'name',
		'Username' => 'username',
		'Password' => 'password',
		'Email' => 'email',
		'Telephone' => 'telephone',
		'Birthday' => 'birthday',
		'GradeName' => 'grade',
		);
}

?>