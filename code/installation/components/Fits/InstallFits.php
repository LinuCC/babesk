<?php

require_once PATH_CODE . '/include/sql_access/DBConnect.php';
require_once PATH_CODE . '/include/functions.php';

/**
 * Installs the program Fits
 * @author Mirek Hancl
 *
 */
class InstallFits extends InstallationComponent {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_smarty;
	private $_templatePath;
	private $_dbInformation;
	private $_db;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($name, $nameDisplay, $path) {

		parent::__construct($name, $nameDisplay, $path);
		$this->setUpSmarty();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function setDBInformation ($host, $username, $password, $databaseName) {

		$this->_dbInformation = array(
			'host'			 => $host,
			'username'		 => $username,
			'password'		 => $password,
			'databaseName'	 => $databaseName,
		);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * Entry-Point for the Babesk-Installation
	 */
	public function execute ($dataContainer) {

		if (isset($_GET['action'])) {

			if ($_GET['action'] != 'dbSetup') {
				$this->initDatabase();
			}

			switch ($_GET['action']) {
				case 'dbSetup':
					$this->setUpDBInformation();
					$this->installDatabaseTables();
					$this->inputDbValues();
					break;
				case 'tableValueSetup':
					$this->addTableValues();
					$this->showInstallationFinished();
					break;
			}
		}
		else {
			$this->inputDBInformation();
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function setUpSmarty () {

		require PATH_CODE . '/smarty/smarty_init.php';
		$this->_smarty = $smarty;
		$this->_templatePath = dirname(__FILE__) . '/templates';
	}

	private function initDatabase () {

		$dbConnection = new DBConnect();
		$dbConnection->initDatabaseFromXML();
		$this->_db = $dbConnection->getDatabase();
	}

	private function createDatabase () {

		$dbArr = $this->_dbInformation;
		$dbConnection = new DBConnect();
		$dbConnection->setDatabaseValues ($dbArr['host'], $dbArr['username'], $dbArr['password'], $dbArr['databaseName']
			);
		$dbConnection->createDatabaseXML(true);
		$this->initDatabase();
	}

	private function inputDBInformation () {

		$this->_smarty->display($this->_templatePath . '/database.tpl');
	}

	private function checkDBInformation ($host, $username, $password, $databaseName) {

		if (!preg_match('/\A\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $host)) {
			die('Das Feld Host wurde nicht korrekt ausgefüllt');
		}
		if (!preg_match('/\A[A-Za-z0-9.,_-]{3,50}\z/', $username)) {
			die('Das Feld Benutzername wurde nicht korrekt ausgefüllt');
		}
		if (!preg_match('/\A[A-Za-z0-9.,_|-]{0,50}\z/', $password)) {
			die('Das Feld Passwort wurde nicht korrekt ausgefüllt');
		}
		if (!preg_match('/\A[A-Za-z0-9]{3,50}\z/', $databaseName)) {
			die('Das Feld Datenbankname wurde nicht korrekt ausgefüllt');
		}
	}

	private function setUpDBInformation () {

		$host = $_POST['Host'];
		$username = $_POST['Username'];
		$password = $_POST['Password'];
		$databaseName = $_POST['Database'];

		$this->checkDBInformation($host, $username, $password, $databaseName);
		$this->setDBInformation($host, $username, $password, $databaseName);
		$this->createDatabase();
	}

	private function installDatabaseTables () {

		$dbTableInstallInformationXML = simplexml_load_file(dirname(__FILE__) . '/tablesToInstall.xml');
		foreach ($dbTableInstallInformationXML->table as $table) {
			$this->installTable($table);
		}
	}

	private function installTable ($simpleXMLElementTable) {

		$result = $this->_db->query((string) $simpleXMLElementTable->sqlString);

		if (!$result) {
			throw new Exception(sprintf('Error adding the table "%s" with the sqlString "%s"', $simpleXMLElementTable->
				name, $simpleXMLElementTable->sqlString));
		}
	}

	private function inputDbValues () {

		$this->_smarty->display($this->_templatePath . '/dbValues.tpl');
	}

	private function addAdminToSQL ($simpleXmlElement) {

		$pw = $_POST['fitsKey'];
		$pwRepeat = $_POST['fitsKeyRepeat'];

// 		if (!preg_match('/\A[A-Za-z0-9]{3,30}\z/', $pw)) {
// 			die('das Passwort wurde falsch eingegeben');
// 		}
		if ($pw != $pwRepeat) {
			die('Die Passwörter stimmen nicht überein');
		}

		$queryStr = sql_prev_inj(sprintf($simpleXmlElement->sqlString, $pw));
		$this->_db->query($queryStr);
	}

	private function addTableValues () {

		$dbCommand = simplexml_load_file(dirname(__FILE__) . '/sqlAlterTableCommands.xml');
		foreach ($dbCommand->command as $command) {
			if ($command->name == 'AddFitsKey') {
				$this->addAdminToSQL($command);
			}
			else {
				$queryStr = sql_prev_inj((string) $command->sqlString);
				$result = $this->_db->query($queryStr);

				if (!$result) {
					echo sprintf('The Command "%s" with the string "%s" was not correctly executed by MySQL', $command->
						name, $command->sqlString);
				}
			}

		}
	}

	private function showInstallationFinished () {

		$this->_smarty->display($this->_templatePath . '/finished.tpl');
	}
}

?>