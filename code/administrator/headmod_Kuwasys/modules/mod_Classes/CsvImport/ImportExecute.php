<?php

namespace administrator\Kuwasys\Classes\CsvImport;

require_once 'CsvImport.php';


class ImportExecute extends \administrator\Kuwasys\Classes\CsvImport {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

			parent::entryPoint($dataContainer);

			$this->importData();
			$query = $this->queryGenerate();
			$this->queryExecute($query);
			$this->_interface->dieSuccess(
				_g('The Classes were successfully imported.'));
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	private function importData() {

		if(!empty($_POST['classes'])) {
			$this->_classes = $_POST['classes'];
		}
		else {
			$this->_interface->dieError(_g('No classes given.'));
		}
	}

	private function queryGenerate() {

		$query = '';

		foreach($this->_classes as $class) {
			$query .= $this->classQueryGenerate($class);
		}

		return $query;
	}

	private function classQueryGenerate($class) {

		$classQuery = 'INSERT INTO `KuwasysClasses`
			(label, description, maxRegistration, registrationEnabled,
				schoolyearId)
			VALUES (
				' . $this->_pdo->quote($class['name']) . ',
				' . $this->_pdo->quote($class['description']) . ',
				' . $this->_pdo->quote((int)$class['maxRegistration']) . ',
				' . $this->_pdo->quote((int)$class['registrationEnabled']) . ',
				@activeSchoolyear);
			SELECT LAST_INSERT_ID() INTO @newClassId;';

		$classQuery .= $this->classteacherQueryGenerate(
			$class['classteacher']);

		$catQuery = 'INSERT INTO `KuwasysClassesInCategories`
			(classId, categoryId) VALUES (
				@newClassId,
				' . $this->_pdo->quote($class['classUnit']) . '
			);
		';
		$classQuery .= $catQuery;

		return $classQuery;
	}

	private function classteacherQueryGenerate($classteachers) {

		$query = '';

		foreach($classteachers as &$ct) {
			if($ct['ID'] == 'CREATE_NEW') {
				$query .= $this->newClassteacherQueryGenerate($ct);
				$query .= 'INSERT INTO KuwasysClassteachersInClasses
					(ClassTeacherID, ClassID) VALUES (@newCtId, @newClassId);';
			}
			else if($ct['ID'] !== 0) {
				$query .= 'INSERT INTO KuwasysClassteachersInClasses
					(ClassTeacherID, ClassID) VALUES (
						' . $this->_pdo->quote($ct['ID']) . ', @newClassId);';
			}
			else {
				// Class should not have any Classteacher
				return '';
			}
		}

		return $query;
	}

	private function newClassteacherQueryGenerate($classteacher) {

		$query = '';

		$classteacher['name'] = trim($classteacher['name']);
		// If a new Classteacher should be created
		$names = explode(' ', $classteacher['name'], 2);
		//If there was no space in the Classteachername, only add surname
		$forename = (count($names) == 2) ? $names[0] : '';
		$surname = (count($names) == 2) ? $names[1] : $names[0];

		$query .= 'INSERT INTO KuwasysClassteachers (forename, name)
			VALUES ('
				. $this->_pdo->quote($forename) . ', '
				. $this->_pdo->quote($surname) . ');
			SELECT LAST_INSERT_ID() INTO @newCtId;';

		return $query;
	}

	private function queryExecute($query) {

		try {
			$stmt = $this->_pdo->exec($query);

		} catch (\PDOException $e) {
			$this->_logger->log('Could not execute the Query!', 'Notice',
				Null, json_encode(array('error' => $e->getMessage()))
			);
			$this->_interface->dieError(_g('Could not execute the Query!'));
		}
	}

	/**
	 * For later ;)
	 */
	private function classesUpload($classes) {

		$cStmt = $this->_pdo->prepare(
			'INSERT INTO `KuwasysClasses` (
				label, description, maxRegistration, registrationEnabled,
				schoolyearId
			) VALUES (
				:name, :description, :maxRegistration, :registrationEnabled,
				@activeSchoolyear
			)
		');
		$cticStmt = $this->_pdo->prepare(
			'INSERT INTO `KuwasysClassteachersInClasses` (
				ClassTeacherID, ClassID
			) VALUES (:classteacherId, :newClassId)
		');
		$ctStmt = $this->_pdo->prepare(
			'INSERT INTO `KuwasysClass'
		);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_classes;
}
?>
