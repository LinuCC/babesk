<?php

namespace administrator\Kuwasys\Classes\CsvImport;

require_once 'CsvImport.php';
require_once PATH_INCLUDE . '/CsvReader.php';

/**
 * Allows the User to review the changes before committing changes to Classes
 */
class Review extends \administrator\Kuwasys\Classes\CsvImport {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		$content = $this->csvParse();
		$this->dataToDisplayCreate($content);
		$this->displayTpl('review.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Parses the given CSV-File into an Array
	 *
	 * @return array The content of the CSV-File with properly named keys
	 */
	private function csvParse() {

		if(!empty($_FILES['csvFile']['tmp_name'])) {
			$reader = new \CsvReader($_FILES['csvFile']['tmp_name'], ';');
		}
		else {
			$this->_logger->log(__METHOD__ . ': Could not find uploaded file',
				'Notice', Null);
			$this->_interface->dieError(_g('Could not find uploaded file'));
		}

		$reader->readContents();
		return $reader->getContents();
	}

	/**
	 * Fetches all needed data of Classteachers and returns them
	 *
	 * @return array Format: <ID> => <name>
	 */
	private function classteachersGetAll() {

		try {
			$stmt = $this->_pdo->query(
				'SELECT ID, CONCAT(forename, name) AS name
				FROM classTeacher WHERE 1'
			);
			return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

		} catch (PDOException $e) {
			$this->_logger->log('Could not fetch the Classteachers.',
				'Notice');
			$this->_interface->dieError(
				_g('Could not fetch the Classteachers.'));
		}
	}

	/**
	 * Fetches all needed data of Classunits and returns them
	 *
	 * @return array Format: <ID> => <translatedName>
	 */
	private function classunitsGetAllKeyValue() {

		try {
			$stmt = $this->_pdo->query('SELECT ID, translatedName
				FROM kuwasysClassUnit');
			return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

		} catch (PDOException $e) {
			$this->_logger->log('Could not fetch the Classunits.', 'Notice');
			$this->_interface->dieError(_g('Could not fetch the days.'));
		}
	}

	/**
	 * Creates an Array containing data defining the Preview-data to display
	 * Assigns the Array directly to Smarty
	 *
	 * @param  array  $csvContent The parsed content of the CSV-File
	 */
	private function dataToDisplayCreate($csvContent) {

		$newData = array();

		foreach($csvContent as $row) {
			$newRow = array();
			$this->classteachersEntryHandle($row, $newRow);
			$this->classUnitEntryHandle($row, $newRow);
			$newRow['name'] = $row['name'];
			$newData[] = $newRow;
		}

		$this->_smarty->assign('classes', $newData);
	}

	private function classteachersEntryHandle($csvRow, &$newRow) {

		$classteachers = $this->classteachersGetAll();

		$ctId = array_search($csvRow['classteacher'], $classteachers);
		if($ctId !== false && $ctId !== NULL) {
			// Classteacher found
			$newRow['classteacher'] = array(
				'ID' => $ctId, 'name' => $newRow['classteacher']);
		}
		else {
			// Classteacher not found, suggest a Classteacher
			$similar = array('ID' => 0, 'dist' => 36767, 'name' => '');

			foreach($classteachers as $ctId => $ctName) {
				$dist = levenshtein($csvRow['classteacher'], $ctName);

				if($dist < $similar['dist']) {
					$similar = array(
						'ID' => $ctId, 'dist' => $dist, 'name' => $ctName,
						'origName' => $csvRow['classteacher']);
				}
			}
			$newRow['classteacherOption'] = $similar;
		}
	}

	private function classUnitEntryHandle($csvRow, &$newRow) {

		$classunits = $this->classunitsGetAllKeyValue();

		$cuId = array_search($csvRow['day'], $classunits);

		if($cuId !== false && $cuId !== NULL) {
			// Classunit found
			$newRow['classUnit'] = array(
				'ID' => $cuId, 'name' => $newRow['classUnit']);
		}
		else {
			// Classunit not found, suggest one
			$similar = array('ID' => 0, 'dist' => 36767);

			foreach($classunits as $cuId => $cuName) {
				$dist = levenshtein($csvRow['day'], $cuName);

				if($dist < $similar['dist']) {
					$similar = array(
						'ID' => $cuId, 'dist' => $dist,
						'name' => $cuName, 'origName' => $csvRow['day']);
				}
			}
			$newRow['classUnitOption'] = $similar;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>