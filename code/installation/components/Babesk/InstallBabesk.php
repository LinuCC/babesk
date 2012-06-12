<?php

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
	public function execute () {

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'dbSetup':
					$this->setUpDBInformation();
					$this->installDatabaseTables();
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
		$this->_templatePath = __DIR__ . '/templates';
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

		$this->setDBInformation($host, $username, $password, $databaseName);
		$this->checkDBInformation($host, $username, $password, $databaseName);
	}

	private function installDatabaseTables () {

		require_once 'DBBabeskInstallation.php';
		$dbInstallationManager = new DBBabeskInstallation($this->_dbInformation['host'], $this->_dbInformation[
			'username'], $this->_dbInformation['password'], $this->_dbInformation['databaseName']);
		try {
			$dbInstallationManager->setupDatabase();
		} catch (Exception $e) {
			die('Konnte die tabellen nicht erstellen: ' . $e->getMessage());
		}
	}
}

?>