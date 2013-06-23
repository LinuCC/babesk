<?php

require_once PATH_INCLUDE . '/Group.php';
require_once PATH_INCLUDE . '/Rightsmanager.php';
require_once PATH_ADMIN . '/AdminInterface.php';

class GroupSettings extends Module {

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

		if(isset($_GET['action']) && 'POST' == $_SERVER['REQUEST_METHOD']) {
			switch($_GET['action']) {
				case 'groupsFetch':
					$this->groupsAllGet();
					break;
				case 'groupsChange':
					$this->groupsChange();
					break;
				case 'modulesFetch':
					$this->modulesFetch();
					break;
				case 'rightChange':
					$this->modulerightStatusChange();
					break;
				default:
					die('Wrong action-value!');
					break;
			}
		}
		else {
			$this->_smarty->display(
				PATH_SMARTY . "/templates/administrator/$this->relPath/" .
				"main.tpl");
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

		$this->_smarty = $dataContainer->getSmarty();
		$this->_acl = $dataContainer->getAcl();
		$this->_interface = new AdminInterface($this->relPath,
			$this->_smarty);
	}

	/**
	 * Fetches all Groups and Outputs them for the JS-Script
	 */
	protected function groupsAllGet() {

		$array = $this->_acl->getGrouproot()->groupAsArrayGet();
		$formatted = $this->groupsFormatForJstree($array);
		die(json_encode(array('value' => 'success',
			'data' => $formatted)));
	}

	/**
	 * Formats the given Group-Array for JSTree, a plugin for JQuery
	 *
	 * @param  Array $groupRootArray The groups as a multidimensional Array
	 */
	protected function groupsFormatForJstree($groupRootArray) {

		$recFuncHelper = array($groupRootArray);
		$groupArray = $this->childsFormatForJstree($recFuncHelper);

		return $groupArray;
	}

	/**
	 * A helper-function formatting an Array
	 *
	 * @param  Array $childs Childs of a Module
	 */
	protected function childsFormatForJstree($childs) {

		if(count($childs)) {
			foreach($childs as &$child) {
				$child['children'] = $child['childs'];
				$child['data'] = $child['name'];
				$child['metadata'] = $child['id'];
				unset($child['childs']);
				unset($child['name']);
				unset($child['id']);
				if(count($child['children'])) {
					$child['children'] = $this->childsFormatForJstree(
						$child['children']);
				}
			}
			return $childs;
		}
		else {
			return array();
		}
	}

	/**
	 * Changes a Group based on the given data
	 */
	protected function groupsChange() {

		$query = '';
		$changeCounter = 0;
		TableMng::sqlSave($_POST['data']);

		if(isset($_POST['data'])) {
			$query = $this->groupsChangeQuery($_POST['data']);
		}
		else {
			die(json_encode(array('value' => 'error',
				'message' => 'No data given!')));
		}

		try {
			TableMng::getDb()->autocommit(false);
			TableMng::query($query, false, true);
			TableMng::getDb()->autocommit(true);

		} catch (Exception $e) {
			die(json_encode(array(
				'value' => 'error',
				'message' => 'Konnte die Query nicht ausführen!')));
		}

		die(json_encode(array(
			'value' => 'success',
			'message' => 'Die Gruppen wurden erfolgreich geändert!')));
	}

	/**
	 * Creates the Correct Query changing the Group
	 *
	 * @param  Array $data The data needed to create the Array
	 */
	protected function groupsChangeQuery($data) {

		$query = '';

		if(!empty($data['action'])) {

			$this->rootChangeWantedCheck($data);

			switch($data['action']) {
				case 'add':
					$query = $this->groupAddQuery($data);
					break;
				case 'rename':
					$query = $this->groupChangeQuery($data);
					break;
				case 'delete':
					$query = $this->groupDeleteQuery($data);
					break;
				default:
					return '';
			}
		}
		else {
			return '';
		}

		return $query;
	}

	/**
	 * Check if user wants to change the root-Node and decline if so
	 *
	 * @param  Array $data The data needed to check for the root-Element
	 */
	protected function rootChangeWantedCheck($data) {

		if(isset($data['name']) && $data['name'] == 'root' ||
			isset($data['oldName']) && $data['oldName'] == 'root') {
			die(json_encode(array(
				'value' => 'error',
				'message' => 'Root darf nicht geändert werden!')));
		}
	}

	/**
	 * Creates a Query to add a Group
	 *
	 * @param  Array $data The data needed to create the Query
	 * @return String The Query
	 */
	protected function groupAddQuery($data) {

		$name = $data['name'];
		$parentPath = $data['parentPath'];

		$parentgroup = $this->_acl->getGrouproot()->groupByPathGet(
			$parentPath);

		if($parentgroup) {
			$query = Group::groupAddQueryCreate($name, $parentgroup);
		}
		else {
			die(json_encode(array(
				'value' => 'error',
				'message' => 'Ein Fehler ist beim Finden der Elterngruppe ' .
					'aufgetreten')));
		}

		return $query;
	}

	/**
	 * Create a Query to change a Group
	 *
	 * @param  Array $data The data needed to create the Query
	 * @return String The Query
	 */
	protected function groupChangeQuery($data) {

		$oldname = TableMng::sqlSave($data['oldName']);
		$oldname = $data['oldName'];
		$newname = TableMng::sqlSave($data['newName']);
		$newname = $data['newName'];
		$parentPath = TableMng::sqlSave($data['parentPath']);
		$parentPath = $data['parentPath'];

		$group = $this->_acl->getGrouproot()->groupByPathGet(
			"$parentPath/$oldname");

		if($group) {
			$query = Group::groupChangeQueryCreate($group, $newname);
		}
		else {
			die(json_encode(array(
				'value' => 'error',
				'message' => 'Ein Fehler ist beim Finden der Gruppe ' .
					'aufgetreten')));
		}

		return $query;
	}

	/**
	 * Creates a Query to delete a Group
	 *
	 * @param  Array $data The data needed to delete the Group
	 * @return String The Query
	 */
	protected function groupDeleteQuery($data) {

		$name = TableMng::sqlSave($data['name']);
		$name = $data['name'];
		$parentPath = TableMng::sqlSave($data['parentPath']);
		$parentPath = $data['parentPath'];

		$group = $this->_acl->getGrouproot()->groupByPathGet(
			"$parentPath/$name");

		if($group) {
			$query = Group::groupDeleteQueryCreate($group);
		}
		else {
			die(json_encode(array(
				'value' => 'error',
				'message' => 'Ein Fehler ist beim Finden der Gruppe ' .
					'aufgetreten')));
		}

		return $query;
	}

	/**
	 * Fetches all Modules, formats them and outputs them for JSTree
	 */
	protected function modulesFetch() {

		TableMng::sqlSave($_POST['grouppath']);

		//init Group
		$group = $this->_acl->getGrouproot()->groupByPathGet(
			$_POST['grouppath']);
		$groupId = $group->getId();
		//init Rights
		$rightArray = TableMng::query("SELECT * FROM GroupModuleRights
			WHERE `groupId` = '$groupId'", true);
		if(count($rightArray)) {
			$rights = GroupModuleRight::initMultiple($rightArray);
		}
		else {
			$rights = array();
		}
		//Init Modules and additional data for them
		$mods = $this->_acl->allowedModulesOfGroupGet($group);
		$modulesJstree = $this->modulesFormatForJstree(
			$mods->moduleAsArrayGet(), $rights);

		die(json_encode(array(
			'value' => 'success',
			'message' => 'Die Daten wurden erfolgreich abgerufen',
			'data' => $modulesJstree)));
	}

	/**
	 * Formats the Modules so that JSTree can process them
	 *
	 * @param  Array $moduleArray An Array of modules
	 * @param  Array $rights An Array of GroupModuleRight-Elements
	 * @return Array The formatted modules
	 */
	protected function modulesFormatForJstree($moduleArray, $rights) {

		$recFuncHelper = array($moduleArray);
		$formattedModules = $this->modulechildsFormatForJstree(
			$recFuncHelper, $rights);

		return $formattedModules;
	}

	/**
	 * A Helper-function to allow JSTree to display the modules
	 *
	 * @param  Array $childs The Childs of the Module
	 * @param  Array $rights The GroupModuleRight-Array
	 * @return Array the changed Childs
	 */
	protected function modulechildsFormatForJstree($childs, $rights) {

		$changeable = false;
		$title = '';

		if(count($childs)) {
			foreach($childs as &$module) {

				$changeable = false;

				if(count($rights)) {
					foreach($rights as $right) {
						if($right->moduleId == $module['id']) {
							$changeable = true;
							continue;
						}
					}
				}

				if($module['enabled']) {
					if($changeable) {
						$title = 'Doppelklick um Modul zu deaktivieren';
					}
					else {
						$title = 'Recht auf dieses Modul wurde von einer übergeordneten Gruppe oder Modul gesetzt; Verändern sie diesen Zugriff dort';
					}
				}
				else {
					$title = 'Doppelklick um Modul zu aktivieren';
				}

				$changeableStr = ($changeable || !$module['enabled']) ?
					'changeable' : 'notChangeable';

				$module['children'] = $module['childs'];
				$module['data'] = $module['name'];
				$module['attr'] = array(
					'id' => 'module_' . $module['id'],
					'module_enabled' => $module['enabled'],
					'rel' => $changeableStr,
					'title' => $title);
				unset($module['enabled']);
				unset($module['childs']);
				unset($module['name']);
				unset($module['id']);
				if(count($module['children'])) {
					$module['children'] = $this->modulechildsFormatForJstree(
						$module['children'], $rights);
				}
			}
			return $childs;
		}
		else {
			return array();
		}
	}

	/**
	 * Changes the Right of a Module
	 */
	protected function modulerightStatusChange() {

		if(!empty($_POST['moduleId']) && !empty($_POST['grouppath'])) {

			$moduleId = $_POST['moduleId'];
			$grouppath = $_POST['grouppath'];
			TableMng::sqlSave($moduleId);
			TableMng::sqlSave($grouppath);

			$group = $this->_acl->getGrouproot()->groupByPathGet($grouppath);
			$module = $this->_acl->allowedModulesOfGroupGet($group);
			if($module->getId() != $moduleId) {
				$module = $module->anyChildByIdGet($moduleId);
			}

			// Reverse the state of the module since the User wants it changed
			$desiredState = !($module->getIsEnabled());

			$this->modulerightStatusChangeUpload( $desiredState, $moduleId,
				$group);

			die(json_encode(array(
				'value' => 'success',
				'message' => 'Die Rechte wurden erfolgreich verändert')));
		}
		else {
			die(json_encode(array('value' => 'error',
				'message' => 'Zu wenig Daten gegeben!')));
		}
	}

	/**
	 * Uploads the Change of a Status to the Database
	 *
	 * @param  boolean $desiredState If the module is enabled or not
	 * @param  int $moduleId The Module-ID
	 * @param  int $group The Group-ID
	 */
	protected function modulerightStatusChangeUpload(
		$desiredState, $moduleId, $group) {

		if($desiredState) {
			GroupModuleRight::rightCreate(
				$moduleId,
				$group->getId());
		}
		else {
			GroupModuleRight::rightDelete(
				$moduleId,
				$group->getId());
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_acl;

	protected $_interface;
}

?>
