<?php

require_once PATH_INCLUDE . '/phpExcel/PHPExcel.php';
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

		if(strstr($this->_filePath, 'csv') !== false) {
			$reader = PHPExcel_IOFactory::createReader('CSV');
			$this->_objPhpExcel = $reader->load($this->_filePath);
		}
		else {
			$this->_objPhpExcel = PHPExcel_IOFactory::load($this->_filePath);
		}
	}

	public function parseFile() {

		$this->_content = $this->_objPhpExcel->getActiveSheet()->toArray(
			null, true, false, false
		);
		$this->_mappedContent = $this->mapContent($this->_content);
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

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_filePath;

	protected $_objPhpExcel;

	protected $_content;

	protected $_mappedContent;
}

?>