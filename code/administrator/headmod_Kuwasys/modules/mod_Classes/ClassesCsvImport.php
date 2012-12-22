<?php

require_once PATH_INCLUDE . '/CsvImporter.php';

/**
 * This class contains the functions needed to import classes by a Csv-File
 */
class ClassesCsvImport {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public static function classInit ($interface, $databaseAccessManager) {
		self::$_interface = $interface;
		self::$_databaseAccessManager = $databaseAccessManager;
	}

	public static function csvFileImport ($filePath, $delimiter) {
		$csvManager = new CsvImporter($filePath, $delimiter);
		$contentArray = $csvManager->getContents();
		$contentArray = self::cellsMissingHandle($contentArray);
		$contentArray = self::schoolyearIdAddByName ($contentArray);
		$contentArray = self::classUnitIdAddByName ($contentArray);
		$contentArray = self::classteacherIdAddByName ($contentArray);
		self::dataToDb ($contentArray);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	protected static function dataToDb ($contentArray) {
		foreach ($contentArray as $rowArray) {
			$classId = self::$_databaseAccessManager->classNextAutoincrementIdGet();
			self::classToDb($rowArray);
			self::jointClassInSchoolyearToDb ($rowArray, $classId);
			self::jointClassteacherInClassToDb ($rowArray, $classId);
		}
	}

	protected static function cellsMissingHandle ($contentArray) {
		foreach ($contentArray as & $rowArray) {
			foreach (self::$_csvStructure as $cell) {
				$rowArray = self::cellMissingHandle ($cell, $rowArray);
			}
		}
		return $contentArray;
	}

	protected static function schoolyearIdAddByName ($contentArray) {
		foreach ($contentArray as &$rowArray) {
			$name = $rowArray [self::$_csvStructure ['SchoolyearName']];
			$schoolyearId = self::$_databaseAccessManager->schoolyearIdGetBySchoolyearNameWithoutDying ($name);
			$rowArray [self::$_csvStructure ['SchoolyearId']] = $schoolyearId;
		}
		return $contentArray;
	}

	protected static function classUnitIdAddByName ($contentArray) {
		foreach ($contentArray as &$rowArray) {
			if ($rowArray [self::$_csvStructure ['ClassUnit']] != '') {
				$classUnit = self::$_databaseAccessManager->kuwasysClassUnitGetByName ($rowArray['weekday']);
				$rowArray [self::$_csvStructure ['ClassUnit']] = $classUnit ['ID'];
			}
		}
		return $contentArray;
	}

	protected static function classteacherIdAddByName ($contentArray) {
		$classteachers = self::$_databaseAccessManager->classteacherGetAllWithoutDieingWhenVoid ();
		if (isset ($classteachers)) {
			foreach ($contentArray as &$rowArray) {
				if ($rowArray [self::$_csvStructure ['ClassteacherName']] != '') {
					$rowArray = self::classteacherIdAddByNameRoutine ($rowArray, $classteachers);
				}
			}
		}
		return $contentArray;
	}

	protected static function classteacherIdAddByNameRoutine ($rowArray, $classteachers) {
		foreach ($classteachers as $classteacher) {
			$ctName = $classteacher ['forename'] . ' ' . $classteacher ['name'];
			if ($rowArray [self::$_csvStructure ['ClassteacherName']] == $ctName) {
				$rowArray [self::$_csvStructure ['ClassteacherId']] = $classteacher ['ID'];
			}
		}
		return $rowArray;
	}

	protected static function cellMissingHandle ($varName, $rowArray) {
		if (!isset($rowArray[$varName])) {
			$rowArray[$varName] = '';
		}
		return $rowArray;
	}

	protected static function classToDb ($rowArray) {
		self::$_databaseAccessManager->classAdd (
			$rowArray [self::$_csvStructure ['Label']],
			$rowArray [self::$_csvStructure ['Description']],
			$rowArray [self::$_csvStructure ['MaxRegistration']],
			$rowArray [self::$_csvStructure ['IsRegistrationEnabled']],
			$rowArray [self::$_csvStructure ['ClassUnit']]);
	}

	protected static function jointClassInSchoolyearToDb ($rowArray, $classId) {
		if($rowArray [self::$_csvStructure ['SchoolyearId']] != '') {
			self::$_databaseAccessManager->jointClassInSchoolyearAdd($rowArray ['schoolyearId'], $classId);
		}
	}

	protected static function jointClassteacherInClassToDb ($rowArray, $classId) {
		$ctId = $rowArray [self::$_csvStructure ['ClassteacherId']];
		if ($ctId != '') {
			self::$_databaseAccessManager->jointClassteacherInClassAdd ($ctId, $classId);
		}
	}


	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	protected static $_interface;
	protected static $_databaseAccessManager;
	protected static $_csvStructure = array (
		'Label' => 'label',
		'Description' => 'description',
		'MaxRegistration' => 'maxRegistration',
		'IsRegistrationEnabled' => 'registrationEnabled',
		'ClassUnit' => 'weekday',
		'SchoolyearName' => 'schoolyearName',
		'SchoolyearId' => 'schoolyearId',
		'ClassteacherName' => 'classteacherName',
		'ClassteacherId' => 'classteacherId',
		);
}

?>