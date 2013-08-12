<?php

abstract class Module {

	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////

	public function __construct ($name, $display_name, $path) {
		$this->name = $name;
		$this->relPath = $path;
		$this->executablePath = $path . $this->name . '.php';
		$this->displayName = $display_name;
	}

	////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////

	public function getName () {
		return $this->name;
	}

	public function getDisplayName () {
		return $this->displayName;
	}

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	public function execute ($dataContainer) {
		require $this->executablePath;
	}

	////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////

	/**
	 * Initializes some Smarty-Variables to display the Website
	 *
	 * Should get called by the Subclass using it since most Modules used
	 * Interfaces specific to them so they dont use and have Smarty
	 */
	protected function initSmartyVariables() {

		$this->_smartyModuleTemplatesPath =
			PATH_SMARTY_ADMIN_TEMPLATES . $this->relPath;

		$siteHeaderPath = $this->_smartyModuleTemplatesPath . 'header.tpl';
		$this->_smarty->assign('inh_path', $siteHeaderPath);
	}

	/**
	 * Checks if the Level of Submodule exists in the SubmoduleExecutionstring
	 * @param  integer $level The Level of Submodule
	 * (1 for the first Submodule)
	 * @return boolean True of it exists, else false
	 */
	protected function execPathHasSubmoduleLevel($level, $path) {

		if($path) {
			$elements = explode('/', $path);
			$submodules = count($elements) - 4;
			return $level <= $submodules;
		}
		else {
			return false;
		}
	}

	/**
	 * Executes a Submodule by calling a Method
	 *
	 * The name of the called Method begins with submodule, goes on with the
	 * modules name and ends with Execute. For Example submoduleUserExecute()
	 *
	 * @param  String $path The Path to the Submodule, beginning from the
	 *                      moduleroot
	 * @param  int level The level of the Submodule (The first submodule
	 *                   is at 1)
	 * @param  string prefix The Prefix of the Methodname to execute
	 * @param  string postfix The Postfix of the Methodname to execute
	 * @return ???    Returns the value that the Submodule returns
	 */
	protected function submoduleExecute($path, $level = 1, $prefix = 'submodule', $postfix = "Execute") {

		$executePath = $this->executionPathSliceToLevels(
			$path,
			$level);
		$submodule = $this->_acl->moduleGet($executePath);
		if($submodule) {
			$methodName = $prefix . $submodule->getName() . $postfix;
			if(method_exists($this, $methodName)) {
				return $this->$methodName();
			}
			else {
				throw new Exception(
					"Submodul-Methode $methodName existiert nicht.<br />");
			}
		}
		else {
			throw new Exception("Zugriff auf dieses Modul nicht erlaubt!");
		}
	}

	private function executionPathSliceToLevels($path, $levelOfSubMod) {

		//levelOfSubMod starts from the Submodule, 4 levels under root
		$levelOfMod = $levelOfSubMod + 4;
		$levels = explode('/', $path);
		if(count($levels) < $levelOfMod) {
			return false;
		}
		$levelsWanted = array_slice($levels, 0, $levelOfMod);
		$executePath = implode('/', $levelsWanted);
		return $executePath;
	}

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	protected $name;
	protected $relPath;
	protected $displayName;
	protected $executablePath;

	/**
	 * The Smarty-Object used to display Information to the User
	 * @var Smarty
	 */
	protected $_smarty;

	/**
	 * Contains the Path to the Templates-folder of this module
	 * @var string
	 */
	protected $_smartyModuleTemplatesPath;
}

?>
