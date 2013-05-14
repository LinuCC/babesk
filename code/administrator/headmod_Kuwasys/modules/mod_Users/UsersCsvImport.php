<?php

// require_once PATH_INCLUDE . '/CsvImporter.php';

class UsersCsvImport {
	//WACKEN
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
	protected static function dataToDb ($rows) {
		self::usersToAddInit ($rows);
		self::usersSetId ();
		self::usersSetToActiveSchoolyear ();
		self::grades ();
		self::upload ();
		self::$_interface->dieMsg ('Die Daten wurden erfolgreich hochgeladen');
	}

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

	protected static function usersToAddInit ($rows) {
		foreach ($rows as $row) {
			self::$_usersToUpload [] = new UCsvIUserToUpload ($row);
		}
	}

	protected static function usersSetToActiveSchoolyear () {
		$active = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::SchoolyearManager, 'getActiveSchoolyear');
		foreach (self::$_usersToUpload as $user) {
			$user->schoolyear = $active;
		}
	}

	protected static function usersSetId () {
		$next = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::UserManager,
			'getNextAIUserId', array());
		foreach (self::$_usersToUpload as $user) {
			$user->id = $next;
			$next ++;
		}
	}

	protected static function grades () {
		$grades = self::gradesFetch ();
		foreach (self::$_usersToUpload as &$user) {
			if ($user->gradeName == '') {
				continue;
			}
			else if ($grade = self::gradesHasGrade ($grades, $user->gradeName)) {
				$user->grade = $grade;
			}
			else {
				$gradeId = self::gradeAdd ($user->gradeName);
				$user->grade = self::gradeMake ($gradeId, $user->gradeName);
			}
		}
		self::gradesSetToActiveSchoolyear ();
	}

	protected static function gradesSetToActiveSchoolyear () {
		if (!count (self::$_gradesToUpload))
			{return;}
		$active = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::SchoolyearManager, 'getActiveSchoolyear');
		foreach (self::$_gradesToUpload as $grade) {
			$grade->schoolyear = $active;
		}
	}

	protected static function gradesFetch () {
		$toFetch = array ();
		foreach (self::$_usersToUpload as $user) {
			if ($user->gradeName == '') {
				continue; // no Grade given, no need to search for it
			}
			$grade = self::gradeSplit ($user->gradeName);
			$row = new DbAMRow ();
			$row->searchFieldAdd ('gradeValue', $grade [0]);
			$row->searchFieldAdd ('label', $grade [1]);
			$toFetch [] = $row;
		}
		if (!count ($toFetch)) {
			return array ();
		}
		$grades = self::$_dbMng->dbAccessExec (
			KuwasysDatabaseAccess::GradeManager, 'getMultipleGrades',
			array($toFetch));
		return $grades;
	}

	protected static function gradesHasGrade ($grades, $gradeName) {
		foreach ($grades as $grade) {
			if ($grade ['gradeValue'] . $grade ['label'] == $gradeName) {
				return $grade;
			}
		}
		if (!count (self::$_gradesToUpload)) {
			return false;
		}
		foreach (self::$_gradesToUpload as $grade) {
			if ($grade->gradeValue . $grade->gradeLabel == $gradeName) {
				return $grade->toArray ();
			}
		}
		return false;
	}

	protected static function gradeAdd ($gradeName) {
		if (!isset(self::$_aiGradeId)) {
			self::$_aiGradeId = self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::GradeManager, 'getNextAIGradeId');
		}
		$gradeId = self::$_aiGradeId;
		$name = self::gradeSplit ($gradeName);
		self::$_gradesToUpload [] = new UCsvIGradeToAdd ($name [1], $name [0], $gradeId);
		self::$_aiGradeId ++;
		return $gradeId;
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

	protected static function gradeMake ($gradeId, $gradeName) {
		$names = self::gradeSplit ($gradeName);
		return array ('ID' => $gradeId,
			'gradeValue' => $names [0],
			'label' => $names [1]
			);
	}

	protected static function upload () {
		try {
			self::userUpload ();
			self::jUserInGradeUpload ();
			self::jUserInSchoolyearUpload ();
			self::gradeUpload ();
			self::jGradeInSchoolyearUpload ();
		} catch (Exception $e) {
			self::$_interface->dieError (sprintf('Nicht alle Daten konnten hochgeladen werden, die Datenbank ist jetzt wahrscheinlich beschädigt. Fehler: %s', $e->getMessage ()));
		}
	}

	protected static function userUpload () {
		$rows = array ();
		foreach (self::$_usersToUpload as $user) {
			$row = new DbAMRow ();
			$row->processFieldAdd (self::$_csvStructure ['Forename'],
				$user->forename);
			$row->processFieldAdd (self::$_csvStructure ['Name'],
				$user->name);
			$row->processFieldAdd (self::$_csvStructure ['Username'],
				$user->username);
			$row->processFieldAdd (self::$_csvStructure ['Password'],
				$user->password);
			$row->processFieldAdd (self::$_csvStructure ['Email'],
				$user->email);
			$row->processFieldAdd (self::$_csvStructure ['Birthday'],
				$user->birthday);
			$row->processFieldAdd (self::$_csvStructure ['Telephone'],
				$user->telephone);
			$rows [] = $row;
		}
		if (count ($rows)) {
			self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::UserManager,
				'addMultipleUser', array ($rows), 'addMultipleUser');
		}
	}

	protected static function jUserInGradeUpload () {
		$rows = array ();
		foreach (self::$_usersToUpload as $user) {
			$row = new DbAMRow ();
			$row->processFieldAdd ('UserID', $user->id);
			$row->processFieldAdd ('GradeID', $user->grade ['ID']);
			$rows [] = $row;
		}
		if (count ($rows)) {
			self::$_dbMng->dbAccessExec (
				KuwasysDatabaseAccess::JUserInGradeManager,
				'addMultipleJoints', array ($rows));
		}
	}

	protected static function jUserInSchoolyearUpload () {
		$rows = array ();
		foreach (self::$_usersToUpload as $user) {
			$row = new DbAMRow ();
			$row->processFieldAdd ('UserID', $user->id);
			$row->processFieldAdd ('SchoolYearID', $user->schoolyear ['ID']);
			$rows [] = $row;
		}
		if (count ($rows)) {
			self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::JUserInSchoolyearManager, 'addMultipleJoints', array ($rows));
		}
	}

	protected static function gradeUpload () {
		if (!count (self::$_gradesToUpload))
			{return;}
		$rows = array ();
		foreach (self::$_gradesToUpload as $grade) {
			$row = new DbAMRow ();
			$row->processFieldAdd ('ID', $grade->gradeId);
			$row->processFieldAdd ('gradeValue', $grade->gradeValue);
			$row->processFieldAdd ('label', $grade->gradeLabel);
			$rows [] = $row;
		}
		if (count ($rows)) {
			self::$_dbMng->dbAccessExec (KuwasysDatabaseAccess::GradeManager,
					'addMultipleGrades', array($rows));
		}
	}

	protected static function jGradeInSchoolyearUpload () {
		if (!count (self::$_gradesToUpload))
			{return;}
		$rows = array ();
		foreach (self::$_gradesToUpload as $grade) {
			$row = new DbAMRow ();
			$row->processFieldAdd ('GradeID', $grade->gradeId);
			$row->processFieldAdd ('SchoolYearID', $grade->schoolyear ['ID']);
			$rows [] = $row;
		}
		if (count ($rows)) {
			self::$_dbMng->dbAccessExec (
				KuwasysDatabaseAccess::JGradeInSchoolyearManager,
				'addMultipleJoints', array ($rows));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_usersToUpload;
	protected static $_gradesToUpload;
	protected static $_aiGradeId; // the id of the grade that is next added

	protected static $_interface;
	protected static $_dbMng;
	public static $_csvStructure = array (
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

class UCsvIUserToUpload {
	public function __construct ($row) {
		$this->forename = $row [UsersCsvImport::$_csvStructure ['Forename']];
		$this->name = $row [UsersCsvImport::$_csvStructure ['Name']];
		$this->username = $row [UsersCsvImport::$_csvStructure ['Username']];
		$this->password = $row [UsersCsvImport::$_csvStructure ['Password']];
		$this->email = $row [UsersCsvImport::$_csvStructure ['Email']];
		$this->telephone = $row [UsersCsvImport::$_csvStructure ['Telephone']];
		$this->birthday = $row [UsersCsvImport::$_csvStructure ['Birthday']];
		$this->gradeName = $row [UsersCsvImport::$_csvStructure ['GradeName']];
	}

	public function birthdayParse () {
		$timestamp = strtotime ($this->birthday);
		$parsed = date ('Y-m-d', $timestamp);
		$this->birthday = $parsed;
	}

	public $id;
	public $grade;
	public $schoolyear;

	public $forename;
	public $name;
	public $username;
	public $password;
	public $email;
	public $telephone;
	public $birthday;
	public $gradeName;

}

class UCsvIGradeToAdd {
	public function __construct ($label, $value, $id) {
		$this->gradeLabel = $label;
		$this->gradeValue = $value;
		$this->gradeId = $id;
	}

	public function toArray () {
		return array ('ID' => $this->gradeId,
			'gradeValue' => $this->gradeValue,
			'label' => $this->gradeLabel);
	}

	public $gradeId;
	public $gradeValue;
	public $gradeLabel;
	public $schoolyear;
}

?>