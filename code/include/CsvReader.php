<?php

/**
 * Parses a CSV-File
 *
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 */
class CsvReader {

	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////
	public function __construct ($pathToCsvFile, $delimiterChar) {

		$this->_pathToCsvFile = $pathToCsvFile;
		$this->_countOfVoidColumns = 0;
		$this->_delimiterChar = $delimiterChar;
	}
	////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	/**
	 * Returns all Keys of the Csv-File (only filled after reading the content)
	 * @return Array The Array of Keys
	 */
	public function getKeys() {
		return $this->_keys;
	}



	public function openFile () {

		$this->_csvFileHandle = fopen($this->_pathToCsvFile, 'r');
	}

	public function readContents () {

		if (!isset($this->_csvFileHandle)) {
			$this->openFile();
		}

		$keyRow = $this->getKeyRow();

		$allValuesArray = array();

		$rowCounter = 0;
		while (($valueRow = $this->readLine()) !== false) {
			//begin with non-void columns, jump over void ones at beginning
			for ($i = $this->_countOfVoidColumns; $i < count($valueRow); $i++) {

				if(isset($valueRow[$i]) && isset($keyRow[$i])) {
					$allValuesArray[$rowCounter][$keyRow[$i]] = $valueRow[$i];
				}
				else {
					throw new Exception('CSV-File is structured wrong!');
				}
			}
			$rowCounter ++;
		}
		$this->_completeArray = $allValuesArray;
	}

	/**
	 * Reads the whole content of the Csv-File and returns it as an Array
	 *
	 * Void data-fields will be returned as a void string
	 *
	 * @return Array
	 */
	public function getContents () {

		if (!isset($this->_completeArray)) {
			$this->readContents();
		}
		return $this->_completeArray;
	}

	////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////

	private function getKeyRow () {

		$keyRow = array();
		$rowNotVoid = false;
		while (!$rowNotVoid) {
			$keyRow = $this->readLine();
			$rowNotVoid = $this->checkKeyRow($keyRow);
			$this->_keys = $keyRow;
		}
		if ($keyRow === false) {
			throw new Exception('Error loading CSV: file is completely void');
		}
		return $keyRow;
	}

	private function checkKeyRow ($keyRow) {

		if ($keyRow === false) {
			return false;
		}
		$tempCountOfVoidColumns = 0;
		for ($i = 0; $i < count($keyRow); $i++) {

			if ($keyRow[$i] == '') {
				$tempCountOfVoidColumns++;
			}
			else {
				$this->_countOfVoidColumns = $tempCountOfVoidColumns;
				return true;
			}
		}
		return false;
	}

	private function readLine () {

		$row = fgetcsv($this->_csvFileHandle, 0, $this->_delimiterChar);
		if ($row === NULL) {
			throw new Exception('Handle of CSV-file is not correct; unable to load CSV-file');
		}
		return $row;
	}

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	private $_pathToCsvFile;
	private $_csvFileHandle;
	private $_countOfVoidColumns;
	private $_completeArray;
	private $_delimiterChar;

	/**
	 * The Column-Names
	 * @var array
	 */
	private $_keys = array();
}

?>