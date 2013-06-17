<?php

require_once PATH_INCLUDE . '/Group.php';
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
		$this->_groupmanager = $dataContainer->getGroupmanager();
		$this->_interface = new AdminInterface($this->relPath,
			$this->_smarty);
	}

	protected function groupsAllGet() {

		$root = $this->_groupmanager->groupRootGet();
		$array = $root->groupAsArrayGet();
		$formatted = $this->groupsFormatForJstree($array);
		die(json_encode(array('value' => 'success',
			'data' => $formatted)));
	}

	protected function groupsFormatForJstree($groupRootArray) {

		$recFuncHelper = array($groupRootArray);
		$groupArray = $this->childsFormatForJstree($recFuncHelper);

		return $groupArray;
	}

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

	protected function groupsChange() {

		if(isset($_POST['data'])) {
			foreach($_POST['data'] as $change) {

			}
		}
		else {
			die(json_encode(array('value' => 'error',
				'message' => 'No data given!')));
		}
	}

	protected function groupChangeQuery($data) {

		if(!empty($data['action'])) {
			switch($data['action']) {
				case 'add':
					$this->groupAddQuery($data['name']);
					break;
				case 'rename':
					break;
				case 'delete':
					break;
				default:
					return false;
			}
		}
		else {
			return false;
		}
	}

	protected function groupAddQuery($name) {

	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_modulemanager;
	protected $_groupmanager;
}

?>
