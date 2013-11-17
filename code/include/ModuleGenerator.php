<?php

/**
 * Represents a module in the Database#table Modules
 *
 * Since Modules are structured hierarchically, an ModuleGenerator also
 * containsall its childs.
 * When executed, this Class creates and executes the proper Module.
 *
 * @author  Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class ModuleGenerator {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($ID, $name, $isEnabled, $rgt, $lft,
		$executablePath, $displayInMenu) {

		$this->_id = $ID;
		$this->_name = $name;
		$this->_isEnabled = $isEnabled;
		$this->_userHasAccess = false;
		$this->_rgt = $rgt;
		$this->_lft = $lft;
		$this->_executablePath = $executablePath;
		$this->_displayInMenu = $displayInMenu;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function getName() {
		return $this->_name;
	}

	public function getId() {
		return $this->_id;
	}

	public function getChilds() {
		return $this->_childs;
	}

	public function isDisplayInMenuAllowed() {
		return (boolean) $this->_displayInMenu;
	}

	/**
	 * Returns a boolean describing if the module can be used by the user
	 *
	 * @return Boolean true if enabled, else false
	 */
	public function isEnabled() {
		return $this->_isEnabled;
	}

	/**
	 * Sets a boolean describing if the module can be used by the user
	 *
	 * @param Boolean $hasAccess true if enabled, else false
	 */
	public function setUserHasAccess($hasAccess) {

		$this->_userHasAccess = $hasAccess;
	}

	/**
	 * Returns if the User has access to this Module
	 */
	public function userHasAccess() {

		return $this->_userHasAccess;
	}

	/**
	 * Loads and Executes the Module
	 *
	 * @todo  Parameter are long outdated; We need to change the parameters,
	 *     but that means changing every existing modulefile, too!
	 */
	public function execute($dataContainer) {

		if(!empty($this->_executablePath) &&
			file_exists(PATH_CODE . "/$this->_executablePath")) {
			require_once PATH_CODE . "/$this->_executablePath";

			$executablePathPieces = explode('/', $this->_executablePath);
			array_shift($executablePathPieces); //remove Subprogram
			array_pop($executablePathPieces); //Remove class-File
			$subPathPart = implode('/', $executablePathPieces);
			$subPath = "/$subPathPart/";

			if(class_exists($classname = $this->_name)) {
				$module = new $classname($classname, $classname, $subPath);
				$module->initAndExecute($dataContainer);
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}

		return true;
	}

	/**
	 * Fetches a ModuleChild beginning by this Instance's Path
	 * @param  String $path The (relative) Path of the module, for
	 *     example root/administrator/Kuwasys/User
	 * @param  boolean $checkThis If true, function checks if path starts with
	 *     this module-instance
	 * @return Module The Module if found, false if not
	 */
	public function childByPathGet($path, $checkThis = true) {

		$tree = explode('/', $path);
		$treeIterator = $this;

		if($checkThis) {
			if(!(array_shift($tree) == $this->_name)) {
				return false;
			}
		}

		//check for each Module in the given path if it exists
		foreach($tree as $wantedNodeName) {
			if(!empty($treeIterator->_childs)) {
				foreach($treeIterator->_childs as $node) {
					//If name of Module found
					if($node->_name == $wantedNodeName) {
						$treeIterator = $node;
						continue 2;
					}
				}
			}
			return false; //Module with name not found
		}
		$module = $treeIterator;

		return $module;
	}

	public function moduleAsArrayGet() {

		$data = array(
			'name' => $this->_name,
			'id' => $this->_id,
			'enabled' => $this->_isEnabled,
			'userHasAccess' => $this->_userHasAccess,
			'displayInMenu' => $this->_displayInMenu);
		$childs = $this->childsAsArrayGet($this->_childs);
		$data['childs'] = $childs;
		return $data;
	}

	public function anyChildByIdGet($id) {

		if(!empty($this->_childs)) {
			foreach($this->_childs as $child) {
				if($child->_id == $id) {
					return $child;
				}
				else {
					if(($ret = $child->anyChildByIdGet($id))) {
						return $ret;
					}
				}
			}
		}
		return false;
	}

	/**
	 * @todo When userHasAccess not set, warning?
	 */
	public function notAllowedChildsRemove() {

		if(count($this->_childs)) {
			foreach($this->_childs as $key => $child) {
				if(isset($child->_userHasAccess)) {
					$allowed = $child->_isEnabled && $child->_userHasAccess;
				}
				else {
					$allowed = $child->_isEnabled;
				}
				if($allowed) {
					$child->notAllowedChildsRemove();
				}
				else {
					unset($this->_childs[$key]);
				}
			}
		}
	}

	public function allowAll() {

		$this->_userHasAccess = true;

		if(count($this->_childs)) {
			foreach($this->_childs as $child) {
				$child->_userHasAccess = true;
				$child->allowAll();
			}
		}
	}

	public function infoJsonGet() {

		$inf = array('Id' => $this->_id,
			'Name' => $this->_name,
			'isEnabled' => $this->_isEnabled,
			'lft' => $this->_lft,
			'rgt' => $this->_rgt,
			'executablePath' => $this->_executablePath,
			'displayInMenu' => $this->_displayInMenu
			);

		return $inf;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function childsAsArrayGet() {

		$childArray = array();
		if(!empty($this->_childs)) {
			foreach($this->_childs as $child) {
				$childArray[] = array(
					'id' => $child->_id,
					'name' => $child->_name,
					'enabled' => $child->_isEnabled,
					'userHasAccess' => $child->_userHasAccess,
					'rgt' => $child->_rgt,
					'lft' => $child->_lft,
					'executablePath' => $child->_executablePath,
					'displayInMenu' => $child->_displayInMenu,
					'childs' => $child->childsAsArrayGet());
			}
		}
		return $childArray;
	}

	public function &anyChildByIdGetAsReference($id) {

		if(!empty($this->_childs)) {
			foreach($this->_childs as &$child) {
				if($child->_id == $id) {
					return $child;
				}
				else {
					if(($ret = $child->anyChildByIdGet($id))) {
						return $ret;
					}
				}
			}
		}
		return NULL;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The ID that is representing the Element in the Database-Table
	 * @var numeric
	 */
	public $_id;

	/**
	 * The name of the Module
	 * @var String
	 */
	public $_name;

	/**
	 * If the module is Enabled in general => if it can be accessed
	 * @var boolean
	 */
	public $_isEnabled;

	/**
	 * If the User is allowed to access the Module
	 */
	public $_userHasAccess = false;

	/**
	 * The Childs of this module
	 * @var Array
	 */
	public $_childs;

	public $_lft;

	public $_rgt;

	public $_smartyTemplatePath;

	public $_executablePath;

	public $_displayInMenu;
}

?>
