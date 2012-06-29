<?php

require_once PATH_CODE . '/include/sql_access/DBConnect.php';
require_once PATH_CODE . '/include/sql_access/TableManager.php';

class InstallKuwasys extends InstallationComponent {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_db;
	private $_smarty;
	private $_templatePath;
	private $_flagAddAdmin;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($name = NULL, $nameDisplay = NULL, $path = NULL) {

		parent::__construct($name, $nameDisplay, $path);
		$this->initSmarty();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function execute () {

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'addAdmin':
					$this->addAdmin($_POST['adminPassword'], $_POST['adminPasswordRepeat']);
					break;
				case "finish":
					$this->finishInstallation();
					break;
				default:
					die('Wrong GET-Parameter action!<br>');
					break;
			}
		}
		else {
			$this->entryInstallation();
		}
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function initSmarty () {

		require PATH_CODE . '/smarty/smarty_init.php';
		$this->_templatePath = dirname(__FILE__) . '/templates';
		$this->_smarty = $smarty;
		$this->_smarty->assign('baseLayout', $this->_templatePath . '/installationLayout.tpl');
	}

	private function addError ($str) {

		$this->_smarty->append('errorStr', '<br>' . $str . '<br>');
	}

	private function addNotice ($str) {

		$this->_smarty->append('noticeStr', '<br>' . $str . '<br>');
	}
	private function displayAndDie () {

		$this->_smarty->display($this->_templatePath . '/installationLayout.tpl');
	}

	private function initDatabase () {

		$dbObject = new DBConnect();
		$dbObject->initDatabaseFromXML();
		$this->_db = $dbObject->getDatabase();
	}

	private function entryInstallation () {

		$this->_flagAddAdmin = false;
		$this->addNotice('<h4>Wenn sie das Bargeldlose Bestellsystem auch installieren wollen:</h4>
		Bitte stellen sie sicher, dass sie zuerst BaBeSK installieren, da es sonst zu Fehlern führt.<br><br>');
		$this->initDatabase();
		$this->installDatabaseTables();
		$this->showAddAdminForm();

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
				if (!$this->isTableExisting('users')) {
					return false;
				}
				$this->tableSpecialTreatmentUsers($table);
				break;
			case 'administrators':
				if (!$this->isTableExisting('administrators')) {
					return false;
				}
				return $this->tableSpecialTreatmentAdministrators($table);
				break;
			default:
				return false;
		}
		return true;
	}
	/**
	 * @used-by InstallKuwasys::checkTablesForSpecialTreatment
	 * @param SimpleXML-Object $table
	 */
	private function tableSpecialTreatmentUsers ($table) {

		if ($this->isTableKeyExisting('users', 'email')) {

			$this->addNotice('Die Tabelle Users enthält schon an das Kurswahlsystem angepasste Spalten.');
		}
		else {
			$result = $this->_db->query($table->alternateSqlCommandForBabesk);
			if (!$result) {
				echo sprintf('Could not change the table Users because: %s<br>', $this->_db->error);
			}
		}
	}

	/**
	 * @used-by InstallKuwasys::checkTablesForSpecialTreatment
	 * @param SimpleXML-Object $table
	 */
	private function tableSpecialTreatmentAdministrators ($table) {

		$this->_flagAddAdmin = true;
	}

	private function showAddAdminForm () {

		if ($this->_flagAddAdmin) {
			$this->_smarty->display($this->_templatePath . '/addAdmin.tpl');
		}
		else {
			$this->_smarty->display($this->_templatePath . '/adminAlreadyAdded.tpl');
		}

	}

	private function addAdmin ($password, $passwordRepeat) {

		if (!preg_match('/\A[A-Za-z0-9]{3,30}\z/', $password)) {

			$this->addError('das Passwort wurde falsch eingegeben');
			$this->displayAndDie();
		}
		if ($pw != $passwordRepeat) {
			$this->addError('Die Passwörter stimmen nicht überein');
			$this->displayAndDie();
		}

		$queryStr = sql_prev_inj(sprintf($simpleXmlElement->sqlString, hash_password($pw)));
		$this->_db->query($queryStr);
	}
	
	private function finishInstallation () {
		
		$this->_smarty->display($this->_templatePath . '/finishedInstallation.tpl');
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
	private function isTableKeyExisting ($tableName, $keyName) {

		$tableManager = new TableManager($tableName);
		return $tableManager->existsKey($keyName);
	}
}

?>
