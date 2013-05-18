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

		if(count($this->_csvColumns) == 1 && !$this->_isSingleColumnAllowed) {
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
				$this->_isPreview = ($_POST['isPreview'] == 'true');
				$this->csvDelimiterCheck();
				$this->isSingleColumnAllowedHandle();
				$this->fieldsAllowedVoidParse();
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
	 * Extracts from the data Ajax send if a single-Column CSV is allowed
	 */
	protected function isSingleColumnAllowedHandle() {

		$this->_isSingleColumnAllowed = ($_POST['isSingleColumnAllowed'] == 'true') ? true : false;
	}

	protected function fieldsAllowedVoidParse() {

		if(!empty($_POST['voidColumnAllowed']) &&
			count($_POST['voidColumnAllowed'])) {

			$columns = json_decode($_POST['voidColumnAllowed']);
			foreach($columns as $col) {
				//Incoming JSON got parsed to Objects, cast it to an array
				$ar = (array) $col;

				foreach($ar as $key => $val) {
					if($val) {
						$this->_keysAllowedVoid[$key] = $val;
					}
				}
			}
		}
	}

	/**
	 * Puts all data into the database
	 */
	abstract protected function upload();

	/**
	 * Handles the Entry of the Data-Upload. Begins Transaction
	 */
	protected function uploadStart() {

		TableMng::getDb()->autocommit(false);
	}

	/**
	 * Finalizes the Data-Upload. Ends Transaction and, on certain
	 * circumstances, rolls the changes back
	 */
	protected function uploadFinalize() {

		if($this->_isPreview) {
			$this->uploadPreview();
			TableMng::getDb()->rollback();
		}
		else {
			if(!count($this->_errors)) {
				var_dump('schinken');
				TableMng::getDb()->query('COMMIT');
			}
			else {
				$this->_errors['fatalError'][] = true;
				//show errors 'n stuff'
				TableMng::getDb()->query('ROLLBACK');
				$this->uploadPreview();
			}
		}
	}

	/**
	 * tests if the upload to the database went good
	 *
	 * dies echoing a JSON-File including some previewed data and error-messages
	 */
	protected function uploadPreview() {

		$this->previewTableCreate();
		$this->errorToReadable();

		$return = array(
			'errors' => $this->_errorStr,
			'errorCount' => $this->_errorCount,
			'preview' => $this->_previewStr,
			'csvColumns' => $this->_csvColumns,
			'keysAllowedVoid' => $this->_keysAllowedVoid,
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

	/**
	 * Creates a Table containing a preview of Database-Changes made when
	 * importing the CSV
	 * @return String The HTML-String directly usable in JS/HTML
	 */
	protected function previewTableCreate() {

		$str = '';
		$tableElements = array();

		if(count($this->_previewData) && count($this->_previewDataHead)) {

			//check if the Element does not screw up the Table-Column-Count
			foreach($this->_previewData as $preview) {
				foreach($preview as $column => $value) {
					if(isset($this->_previewDataHead[$column])) {
						$tableElements[][$column] = $value;
					}
				}
			}

			$tableRowStr = $this->previewTableBodyCreate();
			$tableHeadStr = $this->previewTableHeadCreate();

			$str = sprintf('<table class="dataTable">%s%s</table>', $tableHeadStr, $tableRowStr);
		}
		else {
			$str = 'Es wurde keine Vorschau von dem Modul erstellt';
		}

		$this->_previewStr = $str;
	}

	protected function previewTableHeadCreate() {

		$tableHeadStr = '<tr>';

		foreach($this->_previewDataHead as $prev) {
			$tableHeadStr .= sprintf('<th>%s</th>', $prev);
		}
		$tableHeadStr .= '</tr>';
		return $tableHeadStr;
	}

	protected function previewTableBodyCreate() {

		$tableRowStr = '';

		foreach($this->_previewData as $preview) {

			$tableRowStr .= '<tr>';
			$rowValStr = array();

			foreach($preview as $column => $value) {
				//make the HTML-line...
				$rowValStr[$column] = sprintf('<td>%s</td>', $value);
			}

			//and sort it so that every column has its correct value
			foreach($this->_previewDataHead as $previewHead) {

				if(isset($rowValStr[$previewHead])) {
					$tableRowStr .= $rowValStr[$previewHead];
				}
				else {
					$tableRowStr .= '<td>UNDEFINED</td>';
				}
			}

			$tableRowStr .= '</tr>';
		}

		return $tableRowStr;
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

	/**
	 * Stores if a single Column in the CSV-File is allowed
	 * @var boolean
	 */
	protected $_isSingleColumnAllowed = false;

	/**
	 * Stores Data to show the User a preview-form
	 * @var array
	 */
	protected $_previewData = array();

	/**
	 * Stores Data to show the User a preview-form. The Head of the table.
	 * @var array
	 */
	protected $_previewDataHead = array();

}

?>