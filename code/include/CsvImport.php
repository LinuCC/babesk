<?php

require_once PATH_INCLUDE . '/CsvReader.php';

/**
 * The Baseclass for importing Csv-files
 *
 * This Class for itself already can accept files uploaded from the client and
 * convert them into an Array. Also it checks every Entry if it is void, and
 * fills the error-log when data is void.
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
abstract class CsvImport {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the BaseClass
	 */
	public function __construct() {

	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Puts all data into the database
	 */
	abstract public function upload();

	/**
	 * With autocommit(false), tests if the upload to the database went good
	 *
	 * echoes a JSON-File including some previewed data and error-messages
	 */
	abstract public function uploadPreview();

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Parses the file to an Array
	 */
	protected function parse() {

		$csvReader = new CsvReader($this->filePath, $this->delimiter);
		$this->contentArray = $csvReader->getContents();
	}

	/**
	 * Checks the content of the Array for void Strings
	 */
	protected function check() {

		foreach($this->contentArray as $row) {
			foreach($row as $key => $element) {
				if($element == '' && !array_key_exists($key)) {
					$errors['voidField'][] = array(
						'row' => $row, 'key' => $key);
				}
			}
		}
	}

	/**
	 * Converts the Errors into a readable format
	 */
	protected function errorToReadable() {

		foreach($errors as $erContainerKey => $errorContainer) {
			foreach($errorContainer as $error) {

				if($erContainerKey == 'voidField') {
					$str = sprintf('Die Spalte %s enthält einen leeren Wert', $error['key']);
				}
				else {
					$str = sprintf('ein Fehler ist mit der Spalte %s aufgetreten', $error['key']);
				}

				$this->readableErrorAdd($str);
			}
		}
	}

	protected function readableErrorAdd($str) {

		$this->errorStr .= sprintf('%s%s', $str, $this->errorDelimiter);
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The Path to the CSV-File to import
	 * @var string
	 */
	protected $filePath = '';

	/**
	 * Stores the Delimiter for the CSV-File
	 * @var string
	 */
	protected $delimiter = ';';

	/**
	 * Stores the Data in an Array
	 * @var Array
	 */
	protected $contentArray = array();

	/**
	 * Every Element gives
	 * @var array
	 */
	protected $keysAllowedVoid = array();

	/**
	 * Contains Errors that can be shown to the user
	 * @var array
	 */
	protected $errors = array();

	/**
	 * a String of HTML-Code containing the Errors user-readable
	 * @var string
	 */
	protected $errorStr = '';

	/**
	 * The delimiter between errors
	 * @var string
	 */
	protected $errorDelimiter = '<br />';

}

?>