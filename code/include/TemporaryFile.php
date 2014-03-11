<?php

define('PATH_TMP_FILES', PATH_INCLUDE . '/tmpFiles');

/**
 * Offers functionality to allow for easy managing of Temporary files
 *
 * Each Instance of this Class represents a Temporary File. They are saved in
 * two ways: One entry in the Database containing various information for that
 * file and the file with the content saved in a specific folder.
 * instead of the Constructor, use the static functions @see init()
 * @see load() or @see loadFromData() to initialize a Temporary File
 *
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class TemporaryFile {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructor of this Class
	 *
	 * Not public, use one of the static functions @see init() @see load()
	 * @see loadFromData()
	 *
	 * @access protected
	 */
	protected function __construct() {

	}

	/**
	 * Initializes a new Temporary File
	 *
	 * @param  String $content The content of the file. Restrictions of
	 * PHP-Function file_put_contents() apply
	 * @param  String $created Datetime the Temporary File was created
	 *     (usually you want to use date()). Supports Timestamps and Standard
	 *     Date-Strings
	 * @param  String $until Datetime until which the Temporary File is
	 *     considered valid. Supports Timestamps and Standard Date-Strings
	 * @param  String $usage A short description of the Temporary File
	 * (max. 64 Characters); gets stored in the Database
	 * @return TemporaryFile The created Instance
	 */
	public static function init($content, $created, $until, $usage = '') {

		$obj = new TemporaryFile();
		$obj->setInitialValues(false, $content, $created, $until, $usage);

		return $obj;
	}

	/**
	 * Loads the data from an already existing Temporary File
	 *
	 * Executes a non-Cached Query to the SQL-Server, slow for big data!
	 *
	 * @param  numeric $id The ID of the Temporary File to fetch the data for
	 * @return TemporaryFile the created Instance
	 */
	public static function load($id) {

		$obj = new TemporaryFile();
		$obj->loadInitialValues($id);

		return $obj;
	}

	/**
	 * Loads the data from the given array without looking them up first
	 *
	 * This function does not executes a Query to the SQL-Server, this is
	 * useful for creating multiple TemporaryFile-Instances. The data do not
	 * get checked, to avoid crazy errors be sure that the data given is
	 * correct
	 *
	 * @param  Array $data An array that can contain the following keys with
	 *     values:
	 *     ["ID" => numeric, "content" => String, "created" => String,
	 *     "until" => String, "location" => String]
	 * @return TemporaryFile the created Instance
	 */
	public static function loadFromData($data) {

		$standardValues = array('ID' => false, 'content' => '',
			'created' => 0, 'until' => 0, 'location' => '');

		foreach($standardValues as $key => $standardVal) {
			if(!isset($data[$key])) {
				$data[$key] = $standardVal;
			}
		}
		$obj = new TemporaryFile();
		$obj->setInitialValues($data['ID'], $data['content'],
			$data['created'], $data['until'], $data['usage'],
			$data['location']);

		return $obj;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Saves the Temporary File
	 *
	 * @return Returns the ID of the Temporary File
	 */
	public function store() {

		TableMng::getDb()->autocommit(false);
		$this->storeToDb();
		$this->storeToFile();
		TableMng::getDb()->autocommit(true);

		return $this->_fileId;
	}

	/**
	 * Removes the Temporary File
	 */
	public function remove() {

		TableMng::getDb()->autocommit(false);
		$this->deleteFromDb();
		$this->deleteFile();
		TableMng::getDb()->autocommit(true);
	}

	/**
	 * Provides the file as download to the User
	 */
	public function download($filename, $mimetype = 'test/plain') {

		if(file_exists($this->_filepath)) {
			if(!headers_sent()) {
				header("Content-disposition: attachment; filename=\"$filename\"");
				header("Content-type: $mimetype");
				readfile($this->_filepath);
			}
			else {
				throw new TemporaryFileException('Some data has already been output to browser', 5);
			}
		}
		else {
			throw new TemporaryFileException('File not found!', 5);
		}
	}

	/**
	 * Removes Temporary Files which are broken or expired
	 *
	 * Checks if there are any broken Temporary Files (missing Database-Entry
	 * or missing Content-File) and deletes them.
	 * Checks if there are any expired Temporary Files and deletes them.
	 *
	 * @return int The count of removed entries
	 */
	public static function clean() {

		$removed = 0;

		$removed += self::cleanBrokenDbLinks();
		$removed += self::cleanBrokenFiles();
		$removed += self::removeExpired();

		return $removed;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Stores the content to a new File
	 */
	protected function storeToFile() {

		$this->tempDir();
		if(file_put_contents($this->_filepath, $this->_content) !== FALSE) {
			return;
		}
		else {
			throw new TemporaryFileException("Could not write the file to $this->_filepath", 1);
		}
	}

	/**
	 * Stores the Attributes of the Temporary file to the Database
	 *
	 * @return [type] [description]
	 */
	protected function storeToDb() {

		//only create new entry when entry is not existing already
		if($this->_fileId === false) {
			try {
				TableMng::query("INSERT INTO SystemTemporaryFiles
						(`created`, `until`, `usage`)
					VALUES
						('$this->_created', '$this->_until', '$this->_usage');
						");
				$this->_fileId = TableMng::getDb()->insert_id;

				$this->filepathCreate();

				//Update the location since only now we know the ID of the file
				$this->_filepath = addslashes($this->_filepath);
				TableMng::query("UPDATE SystemTemporaryFiles
					SET `location` = '$this->_filepath'
					WHERE ID = $this->_fileId");

			} catch (Exception $e) {
				throw new TemporaryFileException('Could not insert data into Database' . $e->getMessage(), 2);
			}
		}
		else {
			throw new TemporaryFileException("The Element with ID $this->_fileId already exists in the Database");
		}
	}

	/**
	 * Checks for the temporary Directory and creates it if not existing
	 * @throws  TemporaryFileException If Directory could not be created
	 */
	protected function tempDir() {

		if(!is_dir(PATH_TMP_FILES)) {
			if(mkdir(PATH_TMP_FILES)) {
				return;
			}
			else {
				throw new TemporaryFileException(
					'Could not create temporary folder', 5);
			}
		}
	}

	/**
	 * Creates the filepath of the temporary file
	 */
	protected function filepathCreate() {

		$this->_filepath = PATH_TMP_FILES . "/$this->_fileId";
	}

	/**
	 * Sets the values of the Instance of the Temporary File by given params
	 * @param numeric $id       The ID of the Temporary File
	 * @param String $content  The content of the Temporary File
	 * @param String $created  The time the Temporary File was created
	 * @param String $until    The time until the Temporary File is valid
	 * @param String $location The location of the Temporary File [optional]
	 */
	protected function setInitialValues($id, $content, $created, $until, $usage, $location = '') {

		$this->_fileId = $id;
		$this->_content = $content;
		$this->_created = self::toDatetime($created);
		$this->_filepath = $location;
		$this->_until = self::toDatetime($until);
		$this->_usage = $usage;
	}

	/**
	 * Fetches Attributes and content of the Temporary File by its ID
	 * @param  numeric $id The ID of the Temporary File to load
	 * @throws  TemporaryFileException If somethings gone wrong
	 */
	protected function loadInitialValues($id) {

		$this->_fileId = $id;
		$file = $this->fetchFiledataFromDb();
		$this->_created = $file['created'];
		$this->_until = $file['until'];
		$location = $file['location'];

		if(file_exists($location)) {
			$this->_filepath = $location;
			$this->loadFilecontent();
		}
		else {
			throw new TemporaryFileException(
				"The file with ID $this->_fileId could not be found", 6);
		}
	}

	/**
	 * Loads the Content of the Temporary File from the File
	 * @throws  TemporaryFileException If file could not be read
	 */
	protected function loadFilecontent() {

		if($content = file_get_contents($this->_filepath) !== false) {
			$this->_content = $content;
		}
		else {
			throw new TemporaryFileException(
				"Could not read the data of file $this->_filepath", 7);
		}
	}

	/**
	 * Checks if the file Exists
	 * @return boolean True if it exists, false if not
	 */
	protected function fileExists() {

		return file_exists($this->_filepath);
	}

	/**
	 * Checks if the tableRow of this Object exists
	 * @return boolean True if the Row exists, false if not
	 */
	protected function tableRowExists() {

		$data = TableMng::query("SELECT COUNT(*) AS count FROM SystemTemporaryFiles
			WHERE ID = '$this->_fileId'");
		$count = $data[0]['count'];
		return ($count != 0);
	}

	/**
	 * Fetches the Attributes of the Temporary File from its data in the Db
	 * @return Array The Attributes of the Temporary File
	 * @throws  TemporaryFileException If Data could not be fetched
	 */
	protected function fetchFiledataFromDb() {

		try {
			$data = TableMng::query("SELECT * FROM SystemTemporaryFiles
				WHERE `ID` = '$this->_fileId'");

		} catch (Exception $e) {
			throw new TemporaryFileException(
				"Could not fetch the database-entry for ID $this->_fileId", 8);
		}

		if(is_array($data) && count($data) != 0) {
			if(count($data) == 1) {
				return $data[0];
			}
			else {
				throw new TemporaryFileException(
					"ID $this->_fileId fetched more than one entry", 9);
			}
		}
		else {
			throw new TemporaryFileException(
				"ID $this->_fileId fetched no entry", 10);
		}
	}

	/**
	 * Deletes the DataRow of this instance from the database
	 * @throws  TemporaryFileException If data could not be deleted
	 */
	protected function deleteFromDb() {

		try {
			TableMng::query(
				"DELETE FROM SystemTemporaryFiles WHERE ID = $this->_fileId");

		} catch (Exception $e) {
			throw new TemporaryFileException("Error deleting the Datarow of"+
				"the TemporaryFile ID:\"$this->_fileId\"; $e->getMessage()");
		}
	}

	/**
	 * Deletes the local file of this instance
	 * @throws  TemporaryFileException If File could not be deleted
	 */
	protected function deleteFile() {

		if(unlink($this->_filepath)) {
			return;
		}
		else {
			throw new TemporaryFileException(
				"Could not delete File with ID $this->_fileId", 11);
		}
	}

	/**
	 * Fetches the Paths of all Files that are in the TempFile-Directory
	 * @return Array Array containing an Path for each file
	 */
	protected static function fetchAllFiles() {

		return glob(PATH_TMP_FILES . "/*");
	}

	/**
	 * Extracts the ID of the Temporary File from its local File-Path
	 * @param  String $path The Path of the Content-File
	 * @return String The filename without extension
	 */
	protected static function extractIdFromFilepath($path) {

		$file = explode('/', $path);
		//One more time for Windows-Path
		$file = explode('\\', end($file));
		//remove file extension
		$filenameAr = explode('.', end($file));
		$filename = $filenameAr[0];
		return $filename;
	}

	/**
	 * Cleans the Table from entries that dont have a local file
	 * @return int The count of the deleted Rows
	 */
	protected static function cleanBrokenDbLinks() {

		$entries = TableMng::query('SELECT * FROM SystemTemporaryFiles');
		$deletedRows = 0;

		if(!count($entries)) {
			return $deletedRows;
		}
		foreach($entries as $entry) {
			$obj = TemporaryFile::loadFromData($entry);
			if(!$obj->fileExists()) {
				$obj->deleteFromDb();
				$deletedRows += 1;
			}
		}

		return $deletedRows;
	}

	/**
	 * Removes the files that dont have a Database-Entry representing them
	 *
	 * @return int The count of deleted files
	 */
	protected static function cleanBrokenFiles() {

		$files = self::fetchAllFiles();
		$deletedFiles = 0;

		if(!count($files)) {
			return $deletedFiles;
		}

		foreach($files as $file) {
			$obj = TemporaryFile::loadFromData( array(
				'ID' => self::extractIdFromFilepath($file),
				'location' => $file));

			if(!$obj->tableRowExists()) {
				$obj->deleteFile();
				$deletedFiles += 1;
			}
		}
	}

	/**
	 * Removes Expired Temporary files
	 * @return int The count of removed Entries
	 */
	protected static function removeExpired() {

		$now = self::toDatetime(time());
		$oldEntries = TableMng::query(
			"SELECT * FROM SystemTemporaryFiles WHERE until < '$now'");

		$deletedEntries = 0;
		if(count($oldEntries)) {
			foreach($oldEntries as $entry) {
				$temp = TemporaryFile::load($entry['ID']);
				$temp->remove();
				$deletedEntries += 1;
			}
		}

		return $deletedEntries;
	}

	/**
	 * Tries to read the $time-Parameter as a datetime and converts it
	 *
	 * @param  String $time A timestamp or a date()-format
	 * @return String The converted time in "Y-m-d H:i:s"-format
	 * @throws TemporaryFileException If time could not be converted
	 */
	protected static function toDatetime($time) {

		$convTime = '';
		$timestamp = false;
		if((string)(int)$time == $time) {
			//assume its a timestamp
			$timestamp = $time;
		}
		else {
			//assume its a string containing a time-format
			if(($timestamp = strtotime($time)) !== FALSE) {
				//good
			}
			else {
				throw new TemporaryFileException("Could not convert the " .
					"time-input \"$time\" to a timestamp", 3);
			}
		}

		if($convTime = date('Y-m-d H:i:s', $timestamp)) {
			return $convTime;
		}
		else {
			throw new TemporaryFileException(
				"Could not convert the string '$time' to time", 4);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The content of the file
	 * @var String
	 */
	protected $_content;

	/**
	 * The whole path of the file
	 * @var String
	 */
	protected $_filepath;

	/**
	 * The time the file was created
	 * @var String, Format YYYY-MM-DD HH:MM:SS
	 */
	protected $_created;

	/**
	 * The moment the temporary file expires
	 * @var String, Format YYYY-MM-DD HH:MM:SS
	 */
	protected $_until;

	protected $_usage;

	/**
	 * The ID of the file used in the DB and the filename
	 * @var [type]
	 */
	protected $_fileId = false;
}

?>
