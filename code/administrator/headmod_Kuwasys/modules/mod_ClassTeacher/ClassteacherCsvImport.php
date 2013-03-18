<?php

require_once PATH_INCLUDE . '/CsvImporter.php';

class ClassteacherCsvImport {
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
		self::$_csvImportManager = new CsvImporter ($filePath, $delimiter);
		$contentArray = self::$_csvImportManager->getContents ();
		$contentArray = self::cellsMissingHandle ($contentArray);
		$contentArray = self::wholeNameHandle ($contentArray);
		self::dataToDb ($contentArray);
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	protected static function cellsMissingHandle ($contentArray) {
		foreach ($contentArray as & $rowArray) {
			foreach (self::$_csvStructure as $cell) {
				$rowArray = self::cellMissingHandle ($cell, $rowArray);
			}
		}
		return $contentArray;
	}

	protected static function cellMissingHandle ($varName, $rowArray) {
		if (!isset($rowArray[$varName])) {
			$rowArray[$varName] = '';
		}
		return $rowArray;
	}

	protected static function missingKeysHandle ($contentArray) {
		foreach ($contentArray as &$rowArray) {
			foreach (self::$_csvStructure as $key) {
				$this->checkCsvImportKeyVariable($key, $rowArray);
			}
		}
		return $contentArray;
	}

	protected static function wholeNameHandle ($contentArray) {
		foreach ($contentArray as &$rowArray){
			if ($rowArray [self::$_csvStructure ['WholeName']] != '') {
				$name = array ('', '');
				$name = explode (' ', $rowArray[self::$_csvStructure ['WholeName']], '2');
				if (count ($name) != 2) {
					self::$_interface->showError (sprintf('Konnte den ganzen Namen "%s" nicht parsen.', $rowArray[self::$_csvStructure ['WholeName']]));
					continue;
				}
				$rowArray [self::$_csvStructure ['Forename']] = $name [0];
				$rowArray [self::$_csvStructure ['Name']] = $name [1];
			}
		}
		return $contentArray;
	}

	protected static function dataToDb ($contentArray) {
		foreach ($contentArray as $rowArray) {
			self::classteacherToDbAdd ($rowArray);
		}
	}

	protected static function classteacherToDbAdd ($rowArray) {
		$name = $rowArray [self::$_csvStructure ['Name']];
		$forename = $rowArray [self::$_csvStructure ['Forename']];
		$adress = $rowArray [self::$_csvStructure ['Adress']];
		$telephone = $rowArray [self::$_csvStructure ['Telephone']];
		self::$_databaseAccessManager->classteacherAdd($name, $forename, $adress, $telephone);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	protected static $_interface;
	protected static $_databaseAccessManager;
	protected static $_csvImportManager;
	protected static $_csvStructure = array (
		'Name' => 'name',
		'Forename' => 'forename',
		'WholeName' => 'wholeName',
		'Adress' => 'adress',
		'Telephone' => 'telephone',
		);
}

?>