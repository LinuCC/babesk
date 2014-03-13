<?php

require_once PATH_3RD_PARTY . '/phpExcel/PHPExcel.php';
require_once PATH_INCLUDE . '/IDataImporter.php';

/**
 * Imports data of spreadsheets/csv into an array with PHPExcel
 */
class SpreadsheetImporter implements IDataImporter {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($pathToFile) {

		$this->_filePath = $pathToFile;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function openFile() {

		if(pathinfo(strtolower($this->_filePath), PATHINFO_EXTENSION)
			!= 'csv'
		) {
			$this->_objPhpExcel = PHPExcel_IOFactory::load($this->_filePath);
			//Workaround for loading .ods-files
			$this->_objPhpExcel->setActiveSheetIndex(0);
		}
		else {
			//Workaround for PHPExcel not autoloading Csv-files
			$reader = PHPExcel_IOFactory::createReader('CSV');
			$this->_objPhpExcel = $reader->load($this->_filePath);
		}
	}

	public function parseFile() {

		$this->_content = $this->_objPhpExcel->getActiveSheet()->toArray(
			null, true, false, false
		);
		$con = $this->mapContent($this->_content);
		$this->_mappedContent = $this->voidRowsRemove($con);
	}

	public function getKeys() {

		return $this->_content[0];
	}

	public function getContent() {

		return $this->_mappedContent;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function getNonMappedContent() {

		$buffer = $this->_content;
		unset($buffer[0]); // Remove Key-Row
		return array_values($buffer);
	}

	protected function mapContent() {

		$keys = $this->getKeys();

		$content = $this->getNonMappedContent();
		foreach($content as &$entry) {
			$entry = array_combine($keys, $entry);
		}

		return $content;
	}

	/**
	 * Removes void rows that are added by (buggy?) PhpExcel
	 */
	protected function voidRowsRemove($rows) {

		foreach($rows as $index => $row) {
			foreach($row as $rowElement) {
				if($rowElement !== NULL) {
					continue 2;
				}
			}
			unset($rows[$index]);
		}

		return $rows;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_filePath;

	protected $_objPhpExcel;

	protected $_content;

	protected $_mappedContent;
}

?>