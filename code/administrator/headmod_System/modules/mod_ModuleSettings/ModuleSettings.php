<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/AdminInterface.php';
require_once PATH_ADMIN . '/headmod_System/System.php';

class ModuleSettings extends System {

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
			$this->executeByAction($_GET['action']);
		}
		else {
			$this->displayTpl('main.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * The entry-Point of the Module, when it gets executed
	 *
	 * @param  DataContainer $dataContainer a instance containing general data
	 */
	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		parent::entryPoint($dataContainer);
		parent::initSmartyVariables();
		$this->_interface = new AdminInterface($this->relPath,
			$this->_smarty);
	}

	protected function executeByAction($action) {

		switch($action) {
			case 'modulesFetch':
				$this->modulesFetch();
				break;
			case 'moduleGet':
				$this->moduleGet();
				break;
			case 'moduleChange':
				$this->moduleChange();
				break;
			case 'moduleAdd':
				$this->moduleAdd();
				break;
			case 'moduleRemove':
				$this->moduleRemove();
				break;
			default:
				die('Wrong Action-value');
				break;
		}
	}

	protected function modulesFetch() {

		$moduleroot = $this->_acl->getModuleroot();
		die(json_encode(array('value' => 'success',
			'data' => $moduleroot->moduleAsArrayGet())));
	}

	protected function moduleGet() {

		if(!empty($_POST['moduleId'])) {
			TableMng::sqlEscape($_POST['moduleId']);
			$module = $this->moduleFromDatabaseGet($_POST['moduleId']);
			$this->moduleGottenOutput($module);
		}
		else {
			die(json_encode(array('value' => 'error',
				'message' => 'Keine Modul-ID übergeben!')));
		}

	}

	protected function moduleFromDatabaseGet($moduleId) {

		try {
			$module = TableMng::query("SELECT * FROM Modules
				WHERE ID = $moduleId");

		} catch (Exception $e) {
			die(json_encode(array('value' => 'error',
				'message' => 'Fehler: Modul nicht gefunden!')));
		}

		return $module[0];
	}

	protected function moduleGottenOutput($module) {
		//No need for these vars to be transferred
		unset($module['lft']);
		unset($module['rgt']);
		die(json_encode(array(
			'value' => 'success',
			'data' => $module
			)
		));
	}

	protected function moduleChange() {

		$this->changeInputEscape();
		if($this->changeValidCheck()) {
			$this->changeUpload();
		}
		die(json_encode(array('value' => 'success',
			'message' => 'Das Modul wurde erfolgreich verändert.')));
	}

	protected function changeInputEscape() {

		TableMng::sqlEscape($_POST['id']);
		TableMng::sqlEscape($_POST['name']);
		TableMng::sqlEscape($_POST['isEnabled']);
		TableMng::sqlEscape($_POST['displayInMenu']);
		TableMng::sqlEscape($_POST['executablePath']);
	}

	protected function changeBooleansParse() {

		$_POST['isEnabled'] = $_POST['isEnabled'] === 'true';
		$_POST['displayInMenu'] = $_POST['displayInMenu'] === 'true';
	}

	protected function changeValidCheck() {

		require_once PATH_INCLUDE . '/gump.php';

		$gump = new Gump();
		$validation = array(
			'id' => array('required|min_len,1|max_len,11|numeric', '', 'ID'),
			'name' => array('required|min_len,1|max_len,255', '', 'name'),
			'isEnabled' => array('required|boolean', '',
				'ist Modul aktiviert'),
			'displayInMenu' => array('required|boolean', '',
				'wird in Menü angezeigt'),
			'executablePath' => array('min_len,1|max_len,255', '',
				'Asuführungspfad'),
			);

		$gump->rules($validation);

		if($gump->run($_POST)) {
			$this->changeBooleansParse();
			return $this->changeCheckForSomethingChanged();
		}
		else {
			die(json_encode(array(
				'value' => 'error',
				'message' => $gump->get_readable_string_errors(false)
				)));
			return false;
		}
	}

	protected function changeCheckForSomethingChanged() {

		$module = $this->moduleFromDatabaseGet($_POST['id']);

		if($module['ID'] != $_POST['id'] ||
			$module['name'] != $_POST['name'] ||
			(boolean) $module['enabled'] != $_POST['isEnabled'] ||
			(boolean) $module['displayInMenu'] != $_POST['displayInMenu'] ||
			$module['executablePath'] != $_POST['executablePath']) {
			return true;
		}
		else {
			die(json_encode(array('value' => 'error',
				'message' => 'Es wurden keine Werte verändert!')));
			return false;
		}
	}

	protected function changeUpload() {

		$id = $_POST['id'];
		$name = $_POST['name'];
		$isEnabled = $_POST['isEnabled'] ? 1 : 0;
		$displayInMenu = $_POST['displayInMenu'] ? 1 : 0;
		$executablePath = $_POST['executablePath'];

		try {
			TableMng::query("UPDATE Modules
				SET name = '$name', executablePath = '$executablePath',
					enabled = '$isEnabled', displayInMenu = '$displayInMenu'
				WHERE id = '$id';");

		} catch (Exception $e) {
			die(json_encode(array('value' => 'error',
				'message' => 'Konnte das Modul nicht ändern!' . $e->getMessage())));
		}
	}

	protected function moduleAdd() {

		if($this->moduleAddDataExist()) {
			$this->moduleAddDataEscape();
			$parentmodule = $this->moduleAddParentmoduleGet();
			$moduleId = $this->moduleAddToDb($parentmodule);
			die(json_encode(array('value' => 'success',
				'message' => 'Das Modul wurde erfolgreich hinzugefügt!',
				'data' => array('moduleId' => $moduleId))));
		}
		else {
			die(json_encode(array('value' => 'error',
				'message' => 'Falsche Daten wurden übergeben!')));
		}
	}

	protected function moduleAddDataExist() {

		return !empty($_POST['name']) &&
			!empty($_POST['parentPath']);
	}

	protected function moduleAddDataEscape() {

		TableMng::sqlEscape($_POST['name']);
		TableMng::sqlEscape($_POST['parentPath']);
	}

	protected function moduleAddParentmoduleGet() {

		$parentmodule =  $this->_acl->getModuleroot()->moduleByPathGet(
			$_POST['parentPath']);

		if(!$parentmodule) {
			die(json_encode(array('value' => 'error',
				'message' => 'Konnte ParentPath nicht auflösen!')));
		}
		else {
			return $parentmodule;
		}
	}

	protected function moduleAddToDb($parentmodule) {

		$query = ModuleGenerator::moduleAddQueryCreate(
			$_POST['name'],
			$parentmodule);

		try {
			TableMng::getDb()->autocommit(false);
			TableMng::queryMultiple($query);
			$id = TableMng::getDb()->insert_id;
			TableMng::getDb()->autocommit(true);
			return $id;

		} catch(Exception $e) {
			die(json_encode(array('value' => 'error',
				'message' => 'Konnte Modul nicht hinzufügen' . $e->getMessage())));
		}
	}

	/**
	 * Removes the Module from the Database
	 */
	protected function moduleRemove() {

		TableMng::sqlEscape($_POST['moduleId']);
		$module = $this->moduleByIdGet($_POST['moduleId']);

		try {
			$this->linksOfModuleRemove($_POST['moduleId']);
			$this->moduleEntryRemoveFromDb($module);

		} catch (Exception $e) {
			$this->_logger->log(
				"Could not delete the Module with Id $_POST[moduleId]",
				'Moderate'
			);
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not remove the Module from ' .
					'the Database!'))));
		}

		$this->_logger->log("Deleted Module with ID $_POST[moduleId]");
		die(json_encode(array('value' => 'success',
			'message' => 'Das Modul wurde erfolgreich entfernt')));
	}

	/**
	 * Gets the Module by Id from the AccessControlLayer
	 *
	 * Dies displaying an Error if the Module could not be fetched
	 *
	 * @param  int    $moduleId The ID of the Module
	 * @return ModuleGenerator  The Module
	 */
	protected function moduleByIdGet($moduleId) {

		$module = $this->_acl->getModuleroot()->anyChildByIdGet(
			$moduleId);

		if($module) {
			return $module;
		}
		else {
			$this->_logger->log("Could not find the Module with Id $moduleId in " . __METHOD__);
			die(json_encode(array('value' => 'error',
				'message' => 'konnte das Modul nicht finden')));
		}
	}

	protected function moduleEntryRemoveFromDb($module) {

		if($query = ModuleGenerator::moduleDeleteQueryCreate($module)) {
			try {
				TableMng::getDb()->autocommit(false);
				TableMng::queryMultiple($query);
				TableMng::getDb()->autocommit(true);

			} catch (Exception $e) {
				die(json_encode(array('value' => 'error',
					'message' => 'Konnte das Modul nicht von der Datenbank löschen!')));
			}
		}
	}

	/**
	 * Removes Entries in other Tables linking to the Module
	 *
	 * Logs on Error
	 *
	 * @param  int    $moduleId The Id of the module
	 * @throws PDOException If links could not be deleted
	 */
	protected function linksOfModuleRemove($moduleId) {

		try {
			$stmt = $this->_pdo->prepare(
				'DELETE FROM GroupModuleRights WHERE moduleId = :moduleId');

			$stmt->execute(array('moduleId' => $moduleId));

		} catch (PDOException $e) {
			$this->_logger->log(
				"Could not remove the Links of Module $moduleId");
			throw $e;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The AccessControlLayer used for getting the Submodules
	 * @var Acl
	 */
	protected $_acl;
}

?>
