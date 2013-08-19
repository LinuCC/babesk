<?php

require_once PATH_INCLUDE . '/CsvReader.php';
require_once PATH_INCLUDE . '/gump.php';

class CsvImportError {

	const VOID_FIELD = 'voidField';
	const DB_UPLOAD = 'dbUpload';
	const FATAL_ERROR = 'fatalError';
}

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

	public function execute($dataContainer) {

		$this->_pdo = $dataContainer->getPdo();
		$this->uploadTake();
		$this->parse();
		$this->check();
		$this->preview();
		$wasUploaded = $this->upload();
		$this->finalize($wasUploaded);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Handles the Data from the Ajax-Call of the Client
	 */
	protected function uploadTake() {

		if(count($_FILES)) {
			$gump = new GUMP();
			/**
			 * @todo  !!ESCAPE HERE!!
			 */
		}
		else {
			$this->errorDie(_g('No File has been uploaded!'));
		}
	}


	/**
	 * Parses the data received from the calling Client
	 */
	protected function parse() {

		$this->inputDataFetch();
		$this->metaSettingsParse();
		$this->missingValuesAddAsVoidString();
		$this->csvDataParse();
	}

	/**
	 * Parses the Data of the uploaded Csv-File
	 */
	protected function csvDataParse() {

		try {
			$csvReader = new CsvReader($this->_filePath, $this->_delimiter);
			$this->_contentArray = $csvReader->getContents();
			$this->_csvColumns = $csvReader->getKeys();

		} catch (Exception $e) {
			$this->errorDie(_g('Could not parse the Csv-File!'));
		}
	}

	/**
	 * Checks the Csv for correctness
	 */
	protected function check() {

		$this->csvSizeCheck();
		$this->uploadCheck();
		$this->validFieldDataCheck();
	}

	protected function csvSizeCheck() {

		if(!count($this->_contentArray)) {
			$this->errorDie(_g('The CSV-File is void!'));
		}
		if(count($this->_csvColumns) == 1 && !$this->_isSingleColumnAllowed) {
			$this->errorDie(_g('The CSV-File has only one column!'));
		}
	}

	protected function uploadCheck() {

		TableMng::getDb()->autocommit(false);
		$this->_pdo->beginTransaction();
		try {
			$this->dataCommit();
			TableMng::getDb()->rollback();
			TableMng::getDb()->autocommit(true);
			$this->_pdo->rollBack();


		} catch (Exception $e) {
			$this->errorDie(_g('Could not upload the CSV-File correctly') . $e->getMessage());
		}
	}

	protected function validFieldDataCheck() {

		$this->voidFieldsCheck();
		$this->gumpCheck();
	}

	protected function voidFieldsCheck() {

		foreach($this->_contentArray as $row) {
			foreach($this->_targetColumns as $wantedColumn => $name) {
				$this->singleFieldCheckForVoid($wantedColumn, $row);
			}
		}
	}


	protected function singleFieldCheckForVoid($wantedColumn, $row) {

		if(isset($row[$wantedColumn]) &&
			trim($row[$wantedColumn]) !== '' &&
			$row[$wantedColumn] !== false) {

			return true;
		}
		else {
			if($this->isFieldAllowedVoid($wantedColumn)) {
				return true;
			}
			else {
				$this->errorAdd(
					array(
						'row' => $row,
						'key' => $wantedColumn,
						'type' => 'voidField')
				);
				return false;
			}
		}
	}

	protected function isFieldAllowedVoid($fieldname) {

		return array_key_exists($fieldname, $this->_keysAllowedVoid);
	}

	protected function gumpCheck() {

		$gump = new GUMP();

		try {
			$gump->rules($this->_gumpRules);
			foreach($this->_contentArray as $con) {
				if(!$gump->run($con)) {
					$this->errorAdd(array(
						'type' => 'inputError',
						'message' => $gump->get_readable_string_errors(true)
						));
				}

			}

		} catch (Exception $e) {
			$this->errorDie(_g('Could not check the Inputdata'));
		}
	}

	/**
	 * Creates Data allowing previewing the CSV-Import to the User
	 *
	 * Sets the internal _previewData
	 */
	protected function preview() {

		$maxRows = 25;

		$counter = 0;
		foreach($this->_contentArray as $con) {
			if($counter < $maxRows) {
				$this->_previewData[] = $this->previewSingleRowCreate($con);
			}
			$counter++;
		}

		$this->previewTableCreate();
	}

	/**
	 * Creates a single Preview-Row
	 *
	 * @param  array $con One Contentrow of the Csv
	 * @return array The Row changed so that it can be used as preview
	 */
	protected function previewSingleRowCreate($con) {

		$row = array();

		foreach($this->_targetColumns as $col => $name) {
			$row[$col] = $this->previewSingleFieldCreate($con, $col);
		}

		return $row;
	}

	/**
	 * Creates a single DataField for previewing
	 *
	 * @param  array $con The ContentRow
	 * @param  array $col The Column of the previewTable
	 * @return string
	 */
	protected function previewSingleFieldCreate($con, $col) {

		if(isset($con[$col])) {
			return $con[$col];
		}
		else {
			return '';
		}
	}

	/**
	 * Converts the Errors into a readable format
	 */
	protected function errorToReadable() {

		$voidColumns = array();

		foreach($this->_errors as $error) {
			if($error['type'] != 'voidField') {
				$this->readableErrorAdd($error['message']);
			}
			else {
				if(isset($voidColumns[$error['key']])) {
					$voidColumns[$error['key']] ++;
				}
				else {
					$voidColumns[$error['key']] = 1;
				}
			}
		}

		foreach($voidColumns as $name => $count) {
			if($count == 1) {
				$str = "Die Spalte $name enthält einen leeren Wert";
			}
			else {
				$str = "Die Spalte $name enthält $count leere Werte";
			}
			$this->readableErrorAdd($str);
		}
	}

	private function readableErrorAdd($str) {

		$this->_errorStr .= sprintf('%s%s', $str, $this->_errorDelimiter);
	}

	protected function inputDataFetch() {

		if(!empty($_FILES['csvFile']) && isset($_POST['isPreview'])) {
			$this->_filePath = $_FILES['csvFile']['tmp_name'];
			$this->_isPreview = ($_POST['isPreview'] == 'true');
		}
		else {
			$this->errorDie(_g('Could not parse the Input-data'));
		}
	}

	protected function metaSettingsParse() {

		$this->csvDelimiterCheck();
		$this->isSingleColumnAllowedHandle();
		$this->fieldsAllowedVoidParse();
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

			$columns = json_decode(
				html_entity_decode($_POST['voidColumnAllowed']));
			if(count($columns)) {
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
	}

	/**
	 * Checks that each Column to insert contains all Keys of targetColumns
	 *
	 * Useful when allowing not uploading all columns, so that MySQL does not
	 * throws its hands up in despair when trying to add NULL to a NOT NULL
	 * field
	 */
	protected function missingValuesAddAsVoidString() {

		foreach($this->_contentArray as &$con) {
			foreach($this->_targetColumns as $col => $colname) {
				if(!isset($con[$col])) {
					$con[$col] = '';
				}
			}
		}
	}

	/**
	 * Puts all data into the database
	 *
	 * @return boolean True if CSV-File successfully uploaded, false on Error
	 * or when it was just a preview
	 */
	protected function upload() {

		$this->uploadStart();
		try {
			$this->dataCommit();
		} catch (Exception $e) {
			$this->errorDie(_g('Could not commit the Data') . $e->getMessage());
		}
		return $this->uploadFinalize();
	}

	/**
	 * Handles the Entry of the Data-Upload. Begins Transaction
	 */
	protected function uploadStart() {

		TableMng::getDb()->autocommit(false);
		$this->_pdo->beginTransaction();
	}

	/**
	 * Pushes the datachanges to the Database.
	 *
	 * Do NOT use autocommit (or Transactions in general) in this function,
	 * this class takes care of the wrapping.
	 */
	abstract protected function dataCommit();

	/**
	 * Finalizes the Data-Upload. Ends Transaction and, on certain
	 * circumstances, rolls the changes back
	 */
	protected function uploadFinalize() {

		if($this->_isPreview) {
			TableMng::getDb()->query('ROLLBACK');
			$this->_pdo->rollBack();
		}
		else {
			if(!count($this->_errors)) {
				TableMng::getDb()->query('COMMIT');
				$this->_pdo->commit();
				return true;
			}
			else {
				TableMng::getDb()->query('ROLLBACK');
				$this->_pdo->rollBack();
				$this->errorDie(_g('Could not upload the CSV-File!'));
			}
		}

		TableMng::getDb()->autocommit(true);
		return false;
	}

	/**
	 * Finalizes the CsvImport and returns data and messages back to the Client
	 *
	 * dies echoing a JSON-File including some previewed data and
	 * error-messages
	 */
	protected function finalize($wasUploaded) {

		$this->errorToReadable();

		$return = array(
			'errors' => $this->_errorStr,
			'errorCount' => count($this->_errors),
			'preview' => $this->_previewStr,
			'csvColumns' => array_flip($this->_targetColumns),
			'keysAllowedVoid' => $this->_keysAllowedVoid,
			'wasUploaded' => $wasUploaded
			);

		$return = json_encode($return);
		die($return);
	}

	protected function csvDelimiterCheck() {

		if(isset($_POST['csvDelimiter'])) {
			$del = $_POST['csvDelimiter'];
			TableMng::sqlEscape($del);
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

		if(count($this->_previewData) && count($this->_targetColumns)) {

			//check if the Element does not screw up the Table-Column-Count
			foreach($this->_previewData as $preview) {
				foreach($preview as $column => $value) {
					if(isset($this->_targetColumns[$column])) {
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

		foreach($this->_targetColumns as $prev) {
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
			foreach($this->_targetColumns as $previewHead => $name) {

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


	protected function errorDie($message, $type = 'error') {

		die(json_encode(array(
			'value' => $type,
			'message' => $message)));
	}

	protected function errorAdd($data) {

		$this->_errors[] = $data;
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
	 * The columns that can be imported
	 * @var array
	 */
	protected $_targetColumns = array();

	protected $_gumpRules = array();

	/**
	 * The PDO-Database-Object
	 * @var PDO
	 */
	protected $_pdo;

}

?>
