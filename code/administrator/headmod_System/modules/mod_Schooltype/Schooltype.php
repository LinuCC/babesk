<?php

require_once PATH_INCLUDE . '/Module.php';
require_once 'SchooltypeInterface.php';
require_once PATH_ADMIN . '/headmod_System/System.php';

/**
 * Allows the Administrator to configure the Schooltypes
 */
class Schooltype extends System {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'addSchooltype':
					$this->add();
					break;
				case 'changeSchooltype':
					$this->change();
					break;
				case 'deleteSchooltype':
					$this->delete();
					break;
				case 'activate':
					$this->isEnabledUpdateEntry(true);
					$this->showAll();
					break;
				case 'deactivate':
					$this->isEnabledUpdateEntry(false);
					$this->showAll();
					break;
				default:
					die('Wrong action-value');
					break;
			}
		}
		else {
			$this->showAll();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		$this->_interface = new SchooltypeInterface($this->relPath,
			$dataContainer->getSmarty());
	}

	/**
	 * Fetches All Schooltypes from the Database
	 * @return Array
	 */
	protected function fetchAll() {

		$data = array();

		try {
			$data = TableMng::query('SELECT * FROM SystemSchooltype;');

		} catch (MySQLVoidDataException $e) {
			$this->_interface->showError('Es sind keine Schultypen vorhanden');

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Schultypen nicht abrufen');
		}
		return $data;
	}

	/**
	 * Fetches a single Schooltype
	 * @return Array Representing the Schooltype
	 */
	protected function fetch($id) {

		try {
			$data = TableMng::query(sprintf(
				'SELECT * FROM SystemSchooltype WHERE `ID` = "%s"', $id));

		} catch (MySQLVoidDataException $e) {
			$this->_interface->dieError('Der Schultyp konnte nicht gefunden werden');

		} catch (Exception $e) {
			$this->_interface->dieError('Der Schultyp konnte nicht abgerufen werden');
		}

		return $data[0];
	}

	/**
	 * Adds the Schooltype with the name $name to the Database
	 * @param string $name The name of the Schooltype
	 */
	protected function dbAddTo($name) {

		try {
			TableMng::query(sprintf(
				'INSERT INTO SystemSchooltype (name) VALUES ("%s")', $name));

		} catch (Exception $e) {
			$this->_interface->dieError(
				'Konnte den Schultypen nicht hinzufügen');
		}
	}

	/**
	 * The User wants to add a Schooltype
	 */
	protected function add() {

		if(isset($_POST['name'])) {
			$name = TableMng::getDb()->real_escape_string($_POST['name']);
			$this->dbAddTo($name);
			$this->_interface->dieMsg(
				'Der Schultyp wurde erfolgreich hinzugefügt');
		}
		else {
			$this->_interface->addSchooltype();
		}
	}

	protected function change() {

		if(isset($_POST['name'])) {
			$id = TableMng::getDb()->real_escape_string($_GET['ID']);
			$name = TableMng::getDb()->real_escape_string($_POST['name']);
			$this->dbChangeTo($id, $name);
			$this->_interface->dieMsg('Der Schultyp wurde erfolgreich verändert');
		}
		else if(isset($_GET['ID'])) {
			$id = TableMng::getDb()->real_escape_string($_GET['ID']);
			$schooltype = $this->fetch($id);
			$this->_interface->changeSchooltype($schooltype);
		}
		else {
			die('keine ID?');
		}
	}

	protected function dbChangeTo($id, $name) {

		try {
			TableMng::query(sprintf(
				'UPDATE SystemSchooltype SET name = "%s" WHERE ID = "%s"', $name,
				$id));

		} catch (Exception $e) {
			$this->_interface->dieError(
				'Konnte den Schultypen nicht verändern');
		}
	}

	protected function delete() {

		if(isset($_GET['ID'])) {
			$id = TableMng::getDb()->real_escape_string($_GET['ID']);

			if(isset($_POST['nonono'])) {
				$this->_interface->dieMsg(
					'Der Schultyp wurde nicht gelöscht');
			}
			else if(isset($_POST['deletePls'])) {
				$this->dbDeleteFrom($id);
				$this->_interface->dieMsg('Der Schultyp wurde erfolgreich gelöscht');
			}
			else {
				$schooltype = $this->fetch($id);
				$this->_interface->deleteSchooltype($schooltype);
			}
		}
	}

	protected function dbDeleteFrom($id) {

		try {
			TableMng::query(sprintf(
				'DELETE FROM SystemSchooltype WHERE `ID` = %s', $id));

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte den Schultypen nicht löschen');
		}
	}

	/**
	 * Checks if the Schooltypes are enabled in the Settings
	 * @return boolean True if the Schooltypes are enabled, else false
	 */
	protected function isEnabled() {
		try {
			$data = TableMng::query('SELECT value FROM SystemGlobalSettings
				WHERE name = "schooltypeEnabled"');

		} catch (MySQLVoidDataException $e) {
			$this->isEnabledAddEntry();
			return false; //default to false if no entry found

		} catch (Exception $e) {
			$this->_interface->showError('Konnte nicht überprüfen, ob Schultypen benutzt werden');
		}

		return ($data[0]['value'] != '0');
	}

	/**
	 * Adds the isEnabled-Entry to the globalSettings and defaults to false
	 * @return void
	 */
	protected function isEnabledAddEntry() {

		try {
			TableMng::query('INSERT INTO SystemGlobalSettings (`name`, `value`)
				VALUES ("schooltypeEnabled", 0)');

		} catch (Exception $e) {
			$this->_interface->dieError('konnte den Schultyp-Switch nicht hinzufügen!');
		}
	}

	protected function isEnabledUpdateEntry($isEnabled) {

		$ie = ($isEnabled) ? '1' : '0';

		try {
			TableMng::query(sprintf('UPDATE SystemGlobalSettings SET value = "%s"
							WHERE name = "schooltypeEnabled"', $ie));

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Schultypen nicht
				(de-)aktivieren');
		}
	}

	protected function showAll() {

		if($this->isEnabled()) {
			$data = $this->fetchAll();
		}
		else {
			$data = false;
		}
		$this->_interface->mainMenu($data);
	}


	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_interface;
}

?>
