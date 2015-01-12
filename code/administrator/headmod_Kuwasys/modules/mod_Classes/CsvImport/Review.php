<?php

namespace administrator\Kuwasys\Classes\CsvImport;

require_once 'CsvImport.php';
require_once PATH_INCLUDE . '/CsvReader.php';
require_once PATH_INCLUDE . '/SpreadsheetImporter.php';

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
		$this->csvDataCheck($content);
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

			$fileWithExtension = $_FILES['csvFile']['tmp_name']  . '.' .
				pathinfo($_FILES['csvFile']['name'], PATHINFO_EXTENSION);
			rename($_FILES['csvFile']['tmp_name'], $fileWithExtension);
			$reader = new \SpreadsheetImporter($fileWithExtension);
			$reader->openFile();
			$reader->parseFile();
			return $reader->getContent();

			// $reader = new \CsvReader($_FILES['csvFile']['tmp_name'], ';');
		}
		else {
			$this->_logger->log(__METHOD__ . ': Could not find uploaded file',
				'Notice', Null);
			$this->_interface->dieError(_g('Could not find uploaded file'));
		}

		// $reader->readContents();
		// return $reader->getContents();
	}

	/**
	 * Fetches all needed data of Classteachers and returns them
	 *
	 * @return array Format: <ID> => <name>
	 */
	private function classteachersGetAll() {

		try {
			$stmt = $this->_pdo->query(
				'SELECT ID, CONCAT(forename, " ", name) AS name
				FROM KuwasysClassteachers WHERE 1'
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
				FROM KuwasysClassCategories');
			return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

		} catch (\PDOException $e) {
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
		$this->_classteachers = $this->classteachersGetAll();

		foreach($csvContent as $row) {
			$newRow = array();
			$this->classteacherEntryHandle($row, $newRow);
			$this->classUnitEntryHandle($row, $newRow);
			$newRow['name'] = $row['name'];
			$newRow['description'] = (isset($row['description'])) ?
				$row['description'] : null;
			$newRow['maxRegistration'] = (isset($row['maxRegistration'])) ?
				$row['maxRegistration'] : null;
			$newRow['registrationEnabled'] =
				(isset($row['registrationEnabled'])) ?
				$row['registrationEnabled'] : null;
			$newRow['isOptional'] = (isset($row['isOptional'])) ?
				$row['isOptional'] : null;
			$newData[] = $newRow;
		}

		$this->_smarty->assign('classes', $newData);
	}

	/**
	 * Checks if the CSV-File contains the needed columns
	 *
	 * It is okay for the fields to be void, but just to be sure check that the user knows what he is doing and only import stuff when CSV-File
	 *
	 * @param  [type] $csvContent [description]
	 * @return [type]             [description]
	 */
	private function csvDataCheck($csvContent) {

		if(!isset(
			$csvContent[0]['name'],
			$csvContent[0]['classteacher'],
			$csvContent[0]['day'],
			$csvContent[0]['isOptional']
		)) {
			$this->_interface->dieError(
				_g('The CSV-File uploaded has not all columns it needs to have. ' .
				'Please upload a CSV-File with all columns!')
			);
		}
	}

	private function classteacherEntryHandle($csvRow, &$displayData) {

		foreach(explode(',', $csvRow['classteacher']) as $name) {
			$this->classteacherDataCreate($name, $displayData['classteacher']);
		}
	}

	private function classteacherDataCreate($classteacherName, &$data) {

		$ctId = array_search($classteacherName, $this->_classteachers);
		if($ctId !== false) {
			$data[] = array(
				'ID' => $ctId,
				'displayOptions' => false,
				'name' => $classteacherName
			);
		}
		else {
			$simCt = $this->mostSimilarClassteachersGet($classteacherName);
			if($simCt) {
				$data[] = array(
					'similar' => $simCt,
					'displayOptions' => true,
					'origName' => $classteacherName
				);
			}
			else {
				$data[] = array(
					'ID' => 0,
					'displayOptions' => true,
					'name' => '',
					'origName' => $classteacherName
				);
			}
		}
	}

	/**
	 * Returns the most similar Classteacher that has the name $toSearch
	 *
	 * @param  string $toSearch The string to compare the classteacher-names to
	 * @return array            an array containing the data of the most
	 *                          similar classteacher or false on error
	 *                          <classteacherId>, <classteacherName>
	 */
	private function mostSimilarClassteachersGet($toSearch) {

		$maxAmount = 8;

		$similar = array();
		if(count($this->_classteachers)) {
			foreach($this->_classteachers as $ctId => $ctName) {
				$lev = levenshtein($toSearch, $ctName);
				$similar[$ctId] = $lev;
			}
			asort($similar);
			$similar = array_slice($similar, 0, $maxAmount, true);
			//Overwrite lev with the classteacher-names
			foreach($similar as $ctId => $lev) {
				$similar[$ctId] = $this->_classteachers[$ctId];
			}
			return $similar;
		}
		else {
			return false;
		}

		$bestDist = 99999;
		$mostSim = array('ID' => 0, 'name' => '');

		if(!count($this->_classteachers)) {
			return false;
		}
		foreach($this->_classteachers as $ctId => $ctName) {

			$dist = levenshtein($toSearch, $ctName);

			if($dist < $bestDist) {
				$mostSim = array('ID' => $ctId, 'name' => $ctName);
				$bestDist = $dist;
			}
		}

		return $mostSim;
	}

	private function classUnitEntryHandle($csvRow, &$newRow) {

		$classCategoriesExisting = $this->_entityManager
			->getRepository('Babesk:ClassCategory')
			->findAll();
		$catsToAdd = explode(',', $csvRow['day']);

		if(!count($catsToAdd)) {
			$this->_interface->dieError(
				'Kein Tag bei einem der Kurse angegeben!'
			);
		}

		/**
		 * The Categories that will be assigned to the class
		 * @var array
		 * [
		 *     {
		 *         'ID' => '<cuId>',
		 *         'name' => '<cuName or suggested name if not found>',
		 *         'originalName' => '<cuOriginalName'
		 *     }
		 * ]
		 */
		$categories = array();

		foreach($catsToAdd as $catToAdd) {
			$categoryFound = false;
			foreach($classCategoriesExisting as $catExisting) {
				if($catExisting->getTranslatedName() == $catToAdd) {
					$categories[] = array(
						'ID' => $catExisting->getId(),
						'name' => $catExisting->getTranslatedName(),
						'originalName' => $catToAdd
					);
					$categoryFound = true;
					break;
				}
			}
			if(!$categoryFound) {
				//ClassCategory not found, suggest
				$similar = array('ID' => 0, 'dist' => 36767);
				foreach($classCategoriesExisting as $catExisting) {
					$dist = levenshtein(
						$catToAdd, $catExisting->getTranslatedName()
					);
					if($dist < $similar['dist']) {
						$similar = array(
							'ID' => $catExisting->getId(),
							'name' => $catExisting->getTranslatedName(),
							'dist' => $dist
						);
					}
				}
				$categories[] = array(
					'ID' => $similar['ID'],
					'name' => $similar['name'],
					'originalName' => $catToAdd
				);
			}
		}

		$newRow['categories'] = $categories;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_classteachers;

}
?>
