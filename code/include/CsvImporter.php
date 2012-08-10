<?php

/**
 * Imports a CSV-File
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class CsvImporter {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($pathToCsvFile, $delimiterChar) {

		$this->_pathToCsvFile = $pathToCsvFile;
		$this->_countOfVoidColumns = 0;
		$this->_delimiterChar = $delimiterChar;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
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
			//begin with not-void columns, jump over void ones
			for ($i = $this->_countOfVoidColumns; $i < count($valueRow); $i++) {

				$allValuesArray[$rowCounter][$keyRow[$i]] = $valueRow[$i];
			}
			$rowCounter ++;
		}
		$this->_completeArray = $allValuesArray;
	}

	public function getContents () {

		if (!isset($this->_completeArray)) {
			$this->readContents();
		}
		return $this->_completeArray;
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	private function getKeyRow () {

		$keyRow = array();
		$rowNotVoid = false;
		while (!$rowNotVoid) {
			$keyRow = $this->readLine();
			$rowNotVoid = $this->checkKeyRow($keyRow);
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

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	private $_pathToCsvFile;
	private $_csvFileHandle;
	private $_countOfVoidColumns;
	private $_completeArray;
	private $_delimiterChar;
}

?>