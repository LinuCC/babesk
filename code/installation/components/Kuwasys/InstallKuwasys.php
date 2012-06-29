<?php

require_once PATH_CODE . '/include/sql_access/DBConnect.php';
require_once PATH_CODE . '/include/sql_access/TableManager.php';

class InstallKuwasys extends InstallationComponent {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_db;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($name = NULL, $nameDisplay = NULL, $path = NULL) {

		parent::__construct($name, $nameDisplay, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute () {

		$this->initDatabase();
		$this->test();
		die();
		$this->installDatabaseTables();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function initDatabase () {

		$dbObject = new DBConnect();
		$dbObject->initDatabaseFromXML();
		$this->_db = $dbObject->getDatabase();
	}
	
	/**
	 * Installs the Tables into the given Database for Kuwasys
	 */
	private function installDatabaseTables () {

		$tables = simplexml_load_file(dirname(__FILE__) . '/tablesToInstall.xml');

		foreach ($tables->table as $table) {

			if (!$this->checkTablesForSpecialTreatment($table->name, $table)) {

				$result = $this->_db->query($table->sqlCommand);
				if (!$result) {
					echo 'Could not add the table "' . $table->name . '"! ' . $this->_db->error . '<br>';
				}
			}
		}
	}

	/**
	 * @used-by InstallKuwasys::installDatabaseTables
	 * @return boolean false if no special Treatment happened for the Table
	 */
	private function checkTablesForSpecialTreatment ($tableName, $table) {

		switch ($tableName) {
			case 'users':
				if(!$this->isTableExisting('users')) {return false;}
				$this->tableSpecialTreatmentUsers($table);
				break;
			case 'administrators':
				if(!$this->isTableExisting('administrators')) {return false;}
				return $this->tableSpecialTreatmentAdministrators($table);
				break;
			default:
				return false;
		}
		return true;
	}

	private function tableSpecialTreatmentUsers ($table) {
		
		
	}

	private function tableSpecialTreatmentAdministrators ($table) {
		
	}

	/**
	 * @used-by InstallKuwasys::installDatabaseTables
	 * @param $tableName The name of the table
	 */
	private function isTableExisting ($tableName) {
		
		$tableManager = new TableManager($tableName);
		return $tableManager->existsTable();
	}
	
	/**
	 * 
	 * @param string $tableName
	 * @param string $entryName
	 */
	private function isTableKeyExisting ($tableName, $entryName) {
		
		$tableManager = new TableManager($tableName);
		$tableManager->existsEntry($key, $value);
	}
}

?>
