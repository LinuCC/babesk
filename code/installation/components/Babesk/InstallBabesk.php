<?php

require_once PATH_CODE . '/include/sql_access/DBConnect.php';
require_once PATH_CODE . '/include/functions.php';

/**
 * Installs the program Babesk
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class InstallBabesk extends InstallationComponent {

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
					$this->inputGroup();
					break;
				case 'addGroup':
					$this->addGroup();
					$this->inputPriceclass();
					break;
				case 'addPriceclass':
					$this->addPriceclass();
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

		$pw = $_POST['adminPassword'];
		$pwRepeat = $_POST['adminPasswordRepeat'];

		if (!preg_match('/\A[A-Za-z0-9]{3,30}\z/', $pw)) {
			die('das Passwort wurde falsch eingegeben');
		}
		if ($pw != $pwRepeat) {
			die('Die Passwörter stimmen nicht überein');
		}

		$queryStr = sql_prev_inj(sprintf($simpleXmlElement->sqlString, hash_password($pw)));
		$this->_db->query($queryStr);
	}

	private function addTableValues () {

		$dbCommand = simplexml_load_file(dirname(__FILE__) . '/sqlAlterTableCommands.xml');
		foreach ($dbCommand->command as $command) {
			if ($command->name == 'AddAdmin') {
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

	private function inputGroup () {

		$this->_smarty->display($this->_templatePath . '/groups.tpl');
	}

	private function addGroup () {

		require_once PATH_CODE . '/include/sql_access/GroupManager.php';

		$groupManager = new GroupManager();
		$groupName = @$_POST['name'];
		$groupMaxCredit = @$_POST['maxCredit'];

		if (isset($_POST['goOn']) && $groupName == '') {
			return;
		}

		if (!preg_match('/\A[A-Za-z0-9öäü]{3,30}\z/', $groupName)) {
			die('Der Gruppenname wurde inkorrekt eingegeben');
		}
		if (!preg_match('/\A\d{1,5}([.,]\d{2})?\z/', $groupMaxCredit)) {
			die('Das Maximale Guthaben der Gruppe wurde inkorrekt eingegeben');
		}

		$groupManager->addGroup($groupName, $groupMaxCredit);

		if (isset($_POST['addAnother'])) {
			$this->inputGroup();
			die();
		}
	}

	private function inputPriceclass () {

		$groups = $this->retrieveGroups();

		$this->_smarty->assign('groups', $groups);
		$this->_smarty->display($this->_templatePath . '/newPriceclass.tpl');
	}

	private function addPriceclass () {

		require_once PATH_CODE . '/include/sql_access/PriceClassManager.php';
		$pcManager = new PriceClassManager();

		$pc_name = $_POST['name'];
		$normal_price = $_POST['n_price'];
		$groups = $this->retrieveGroups();
		$highestPriceclassID = $pcManager->getHighestPriceclassID();

		if(isset($_POST['goOn']) && trim($_POST['name']) == '') {
			$this->showInstallationFinished();
			return;
		}
		if (!preg_match('/\A^[0-9]{1,2}((,|\.)[0-9]{2})?\z/', $normal_price)) {
			die('Der StandardPreis wurde falsch eingegeben');
		}

		foreach ($groups as $group) {
			$price = $_POST['group_price' . $group['ID']];
			if (!$price || trim($price) == '') {
				$price = $normal_price;
			}
			else if (!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $price)) {
				die('der Preis "' . $price . '" wurde falsch eingegeben');
			}
			$price = str_replace(',', '.', $price); //Comma bad for MySQL
			try { //add the group
				$pcManager->addPriceClass($pc_name, $group['ID'], $price, $highestPriceclassID + 1);
			} catch (Exception $e) {
				echo 'Ein Fehler ist beim Hinzufügen der Preisklasse "' . $pc_name . '" für die GruppenID "' . $group[
					'ID'] . '" aufgetreten.';
			}
		}

		if(isset($_POST['addAnother'])) {
			$this->inputPriceclass();
		}
		else {
			$this->showInstallationFinished();
		}
	}

	private function retrieveGroups() {

		require_once PATH_CODE . '/include/sql_access/GroupManager.php';
		$groupManager = new GroupManager();

		try {
			$groups = $groupManager->getTableData();
		} catch (MySQLVoidDataException $e) {
			echo 'Es sind keine Gruppen vorhanden, somit konnten keine Preisklassen erstellt werden';
			return;
		} catch (Exception $e) {
			die('Could not fetch the Groups');
		}

		return $groups;
	}

	private function showInstallationFinished () {

		$this->_smarty->display($this->_templatePath . '/finished.tpl');
	}
}

?>