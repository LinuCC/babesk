<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * Wraps the Module-Execution-Command (a Path) in a Class
 *
 * Provides useful functions for handling that Command
 */
class ModuleExecutionCommand {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($modExecPath) {

		$this->modExecPathParse($modExecPath);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns the path for the module-execution
	 *
	 * @return string The module-path preceded with root/<subprogram>
	 */
	public function pathGet() {

		$prePath = implode($this->delim, $this->_execPathPreElements);
		$modPath = implode($this->delim, $this->_execPathModules);

		return $prePath . $this->delim . $modPath;
	}

	/**
	 * Returns only the module-part of the execution-path
	 *
	 * @return string the module-path
	 */
	public function modulePathGet() {

		return implode($this->delim, $this->_execPathModules);
	}

	/**
	 * Returns the hierarchical position of the module in this command
	 *
	 * @param  Module $module The module to determine its position
	 * @return int            The Position (starting from 0) or false if not
	 *                        found
	 */
	public function positionOfModuleInPathGet($module) {

		$modulePath = $this->ancestorModulePathGetByModule($module);

		if(strpos($this->pathGet(), $modulePath) !== false) {
			/**
			 * The position of the Module is the Count of its parents and
			 * itself added with 2, since the Executionpath has 2 Elements
			 * preceding the modules (root/administrator or root/web)
			 * minus one to begin the count from 0 instead of one
			 */
			$position = count(explode('/', $modulePath)) + 1;

			return $position;
		}
		else {
			return false;
		}
	}

	/**
	 * Removes the last Module-Element in the Command
	 * @return bool  true on success, false if no Elements are in the Command
	 */
	public function lastModuleElementRemove() {

		if(count($this->_execPathModules)) {
			array_pop($this->_execPathModules);
			return true;
		}
		else {
			return false;
		}

	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Parses a ModuleExecutionPath into the class-intern representation
	 *
	 * @param  string $modExecPath The Execution-Path, beginning with root.
	 *                             The Elements sould be separated by a "/"
	 */
	protected function modExecPathParse($modExecPath) {

		$elements = explode($this->delim, $modExecPath);

		if($elements[0] == 'root') {

			//First two elements are PreElements
			$this->_execPathPreElements[] = array_shift($elements);
			$this->_execPathPreElements[] = array_shift($elements);

			//The leftovers belong to the ModulePath
			$this->_execPathModules = $elements;
		}
		else {
			throw new Exception('Could not parse the ModuleExecutionPath ' .
				"'$modExecPath'!");
		}
	}

	/**
	 * Determines the module path of an module by its class-inheritance
	 *
	 * @param  Module $module The module to determine its class-inheritcance
	 * @return string         the Path
	 */
	protected function ancestorModulePathGetByModule($module) {

		$classes = array_reverse($this->ancestorsGet($module));
		// We dont want the "Module"-Class, not needed in the Path
		array_shift($classes);
		$modulePath = implode($this->delim, $classes);

		return $modulePath;
	}

	/**
	 * Returns all Ancestors of the Object and itself starting by the lowest
	 *
	 * @return array  The Ancestors and the Class itself
	 */
	protected function ancestorsGet($obj) {

		$class = get_class($obj);

		for($classes[] = $class;
			$class = get_parent_class($class);
			$classes[] = $class);

		return $classes;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * The pre-elements in the path represent the root- and the Subprogram-Node
	 * @var array <"string">
	 */
	protected $_execPathPreElements;

	/**
	 * These elements represent the modules in the execution-path
	 * @var array <"string">
	 */
	protected $_execPathModules;

	public $delim = '/';
}

?>
