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

	public function execute() {

		$this->uploadTake();
		$this->parse();
		$this->check();
		$this->upload();
		$this->finalize();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Parses the file to an Array
	 */
	protected function parse() {

		try {
			$csvReader = new CsvReader($this->_filePath, $this->_delimiter);
			$this->_contentArray = $csvReader->getContents();
			$this->_csvColumns = $csvReader->getKeys();

		} catch (Exception $e) {
			die('wrongCsvStructure');
		}
	}

	/**
	 * Checks the content of the Array for void Strings
	 */
	protected function check() {

		if(!count($this->_contentArray)) {
			die('voidCsv');
		}

		if(count($this->_csvColumns) == 1) {
			die('tinyCsv');
		}

		foreach($this->_contentArray as $row) {
			foreach($row as $key => $element) {
				if($element == ''
					&& !array_key_exists($key, $this->_keysAllowedVoid)) {
					$this->_errors['voidField'][] = array(
						'row' => $row, 'key' => $key);
				}
			}
		}
	}

	/**
	 * Converts the Errors into a readable format
	 */
	protected function errorToReadable() {

		$voidContainer = array();
		foreach($this->_errors as $erContainerKey => $errorContainer) {
			foreach($errorContainer as $error) {

				if($erContainerKey == 'voidField') {
					if(isset($voidContainer[$error['key']])) {
						$voidContainer[$error['key']] ++;
					}
					else {
						$voidContainer[$error['key']] = 1;
					}
				}
			}
		}
		foreach($voidContainer as $name => $count) {
			if($count == 1) {
				$str = sprintf('Die Spalte %s enthält einen leeren Wert',
					$name);
			}
			else {
				$str = sprintf('Die Spalte %s enthält %s leere Werte', $name,
					$count);
			}
			$this->readableErrorAdd($str);
		}

		foreach($this->_errors as $erContainerKey => $errorContainer) {
			foreach($errorContainer as $error) {

				$this->_errorCount++;
				if($erContainerKey == 'voidField') {
					//we did these already
					continue;
				}
				else if($erContainerKey == 'dbUpload') {
					$str = sprintf('Ein Fehler ist beim Hochladen von %s aufgetreten', $error['forename'] . $error['name']);
				}
				else if($erContainerKey == 'fatalError') {
					$str = sprintf('Fehler sind aufgetreten; Konnte die CSV-Datei nicht importieren');
				}
				else {
					$str = sprintf('ein Fehler ist mit der Spalte %s aufgetreten', $error['key']);
				}

				$this->readableErrorAdd($str);
			}
		}
	}

	protected function readableErrorAdd($str) {

		$this->_errorStr .= sprintf('%s%s', $str, $this->_errorDelimiter);
	}

	/**
	 * Handles the Data from the Ajax-Call of the Client
	 */
	protected function uploadTake() {

		if(count($_FILES)) {
			if(!empty($_FILES['csvFile']) && isset($_POST['isPreview'])) {
				$this->_filePath = $_FILES['csvFile']['tmp_name'];
				$this->_isPreview = (boolean) $_POST['isPreview'];
				$this->csvDelimiterCheck();
			}
			else {
				die('errorWrongData');
			}
		}
		else {
			die('errorNoFile');
		}
	}

	/**
	 * Puts all data into the database
	 */
	abstract protected function upload();

	protected function finalize() {

		if($this->_isPreview) {
			$this->uploadPreview();
		}
		else {
			if(!count($this->_errors)) {
				TableMng::getDb()->autocommit(true);
			}
			else {
				$this->_errors['fatalError'][] = true;
				//show errors 'n stuff'
				$this->uploadPreview();
			}
		}
	}

	/**
	 * With autocommit(false), tests if the upload to the database went good
	 *
	 * echoes a JSON-File including some previewed data and error-messages
	 */
	protected function uploadPreview() {
		$this->errorToReadable();
		$return = array(
			'errors' => $this->_errorStr,
			'errorCount' => $this->_errorCount,
			'preview' => $this->_previewStr,
			'csvColumns' => $this->_csvColumns
			);

		$return = json_encode($return);
		die($return);
	}

	protected function csvDelimiterCheck() {

		if(isset($_POST['csvDelimiter'])) {
			$del = $_POST['csvDelimiter'];
			TableMng::sqlSave($del);
			if(!empty($del)) {
				$this->_delimiter = $del;
			}
		}
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The Path to the CSV-File to import
	 * @var string
	 */
	protected $_filePath = '';

	/**
	 * Stores the Delimiter for the CSV-File
	 * @var string
	 */
	protected $_delimiter = ',';

	/**
	 * Stores the Data in an Array
	 * @var Array
	 */
	protected $_contentArray = array();

	/**
	 * Every Element gives
	 * @var array
	 */
	protected $_keysAllowedVoid = array();

	/**
	 * Contains Errors that can be shown to the user
	 * @var array
	 */
	protected $_errors = array();

	/**
	 * a String of HTML-Code containing the Errors user-readable
	 * @var string
	 */
	protected $_errorStr = '';

	/**
	 * Keeps track of the Count of errors
	 * @var integer
	 */
	protected $_errorCount = 0;

	/**
	 * The delimiter between errors
	 * @var string
	 */
	protected $_errorDelimiter = '<br />';

	/**
	 * Stores if the CsvUpload should be processed as a preview or uploaded
	 * @var boolean
	 */
	protected $_isPreview = true;

	/**
	 * Contains the preview of the import of the CSV-File
	 * @var string
	 */
	protected $_previewStr = 'Not coded (yet)';

	/**
	 * Contains the Columns of the CSV-File
	 * @var array
	 */
	protected $_csvColumns = array();

}

?>