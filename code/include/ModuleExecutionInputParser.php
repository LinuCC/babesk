<?php

class ModulepathLevel {

	const ROOT = 0;
	const SUBPROGRAM = 1;
	const HEADMODULE = 2;
	const MODULE = 3;
	const SUBMODULE = 4;
}

/**
 * Parses the Input stating which Module should be executed
 *
 * Usage:
 * Standard-way is to construct this class without any Parameter,then calling
 * load(). This function searches for appropiate data in the container GET,
 * POST and SESSION and tries to convert it. Then you can call
 * moduleExecutionGet() to get the path to execute a module
 */
class ModuleExecutionInputParser {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($manualPath = '') {

		if(!empty($manualPath)) {
			$this->_executionPath = str_replace(
				'/',
				$this->_pathDelim,
				$manualPath);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Sets the Subprogrampath. Needed when using the old section-styl
	 *
	 * When using old-style Headmodule|Module as executionpath you need to use
	 * this function beforehand. For example the administrator-Subprogram needs
	 * to give the String 'root/administrator'
	 *
	 * @param String $path The Path to the Subprogram
	 */
	public function setSubProgramPath($path) {
		$path = str_replace('/', $this->_pathDelim, $path);
		$this->_subProgramPath = $path;
	}

	/**
	 * Checks for and loads the Executionpath automatically
	 *
	 * Supported Containers are GET, POST and SESSION
	 *
	 * @return boolean True if everything went well, else false
	 */
	public function load() {

		try {
			if($this->_usedContainer = $this->usedContainerGet()) {
				$path = $this->_usedContainer['module'];
				$this->_executionPath =
					$this->rootAddToExecutionpathIfNotExists($path);
			}
			else {
				return false;
			}

		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the whole Executionpath
	 *
	 * @return String The Path of the Execution
	 */
	public function executionGet() {

		return $this->internalDelimReplaceWithStandard($this->_executionPath);
	}

	/**
	 * Returns the Path executing the Module
	 *
	 * @return String Moduleexecutionpath
	 */
	public function moduleExecutionGet() {

		return $this->internalDelimReplaceWithStandard($this->_executionPath);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function usedContainerGet() {

		if(isset($_GET['module'])) {
			return $_GET;
		}
		else if(isset($_POST['module'])) {
			return $_POST;
		}
		else if(isset($_SESSION['module'])) {
			return $_SESSION;
		}
		else if(isset($_GET['section'])) {
			$_GET['module'] = $this->oldStyleToExecPath($_GET);
			return $_GET;
		}
		else {
			return false;
		}
	}

	protected function internalDelimReplaceWithStandard($string) {

		return str_replace($this->_pathDelim, '/', $string);
	}

	/**
	 * Converts the old-style Moduleexecution-Notation to an Executionpath
	 * @param  Array $container The container containing section (and possibly
	 * action)
	 * @return String The Executionpath
	 */
	protected function oldStyleToExecPath($container) {

		if(preg_match('/^[^\/\|]+\|[^\/\|]+$/', $container['section'])) {
			return $this->headmoduleAndModuleToPath($container['section']);
		}
		else if(preg_match('/^[a-zA-Z]+$/', $container['section'])) {
			return $this->headmoduleToPath($container['section']);
		}
		else {
			throw new Exception('Could not parse section!');
		}
	}

	/**
	 * Creates the Path to the Module from the old-style section-string
	 *
	 * @param  string $section Sectionstring, foramt 'Headmodule|Module'
	 * @return string          The Path
	 * @throws Exception If Suprogrampath not sert
	 */
	protected function headmoduleAndModuleToPath($section) {

		if(!empty($this->_subProgramPath)) {
			return $this->fromHeadmoduleAndModuleCreatePath($section);
		}
		else {
			throw new Exception('Subprogrampath not set but old-style Sectionpath given');
		}
	}

	/**
	 * Creates the Path to the Module from the old-style section-string
	 *
	 * If the Parameter action is given, it will be handled like a submodule
	 *
	 * @param  string $section Sectionstring, foramt 'Headmodule|Module'
	 * @return string The Executionpath
	 */
	protected function fromHeadmoduleAndModuleCreatePath($section) {

		$modSubPath = explode('|', $section);
		$headmod = $modSubPath[0];
		$mod = $modSubPath[1];
		$createdPath = $this->_subProgramPath . "{$this->_pathDelim}$headmod" .
			"{$this->_pathDelim}$mod";
		if(!empty($_GET['action'])) {
			$createdPath .= "{$this->_pathDelim}$_GET[action]";
		}
		return $createdPath;
	}

	/**
	 * Converts the Old-style Headmodule-Execution to a Executionpath
	 *
	 * @param  String $section The String giving the Headmodule to execute
	 * @return String The Executionpath of the Headmodule
	 */
	protected function headmoduleToPath($section) {

		if(!empty($this->_subProgramPath)) {
			return $this->_subProgramPath . "{$this->_pathDelim}$section";
		}
		else {
			throw new Exception('Subprogrampath not set but old-style Sectionpath given');
		}
	}

	/**
	 * Strips the whole Executionpath to only the path executing the Module
	 *
	 * @param  String $path The Path to strip
	 * @return String The stripped Path
	 */
	protected function pathStripToModuleExecutionpath($path) {

		$levels = explode($this->_pathDelim, $path);
		$lastLevel = $this->lowestModuleExecutionOfPathGet($levels);
		$strippedPath = '';

		if($lastLevel) {
			for($i = ModulepathLevel::ROOT; $i <= $lastLevel; $i++) {
				$strippedPath .= $levels[$i] . $this->_pathDelim;
			}
			$strippedPath = rtrim($strippedPath, $this->_pathDelim);

			return $strippedPath;
		}
		else {
			return false;
		}
	}

	/**
	 * Strips the Executionpath from the Elements before the SubmoduleExecution
	 *
	 * @param  String $path The whole Executionpath
	 * @return String The Executionpath starting from the Submodule or false
	 * if given path had no Submodule-Execution
	 */
	// protected function pathStripToOnlySubmoduleExecutionpath($path) {

	// 	$levels = explode($this->_pathDelim, $path);
	// 	if(count($levels) >= ModulepathLevel::SUBMODULE) {
	// 		for($i = ModulepathLevel::ROOT; $i <= ModulepathLevel::MODULE;
	// 			$i++) {
	// 			unset($levels[$i]);
	// 		}

	// 		return implode($this->_pathDelim, $levels);
	// 	}
	// 	else {
	// 		return false;
	// 	}
	// }

	/**
	 * Check if only an Headmodule gets executed or an Module, too
	 *
	 * @param  Array $levels The levels of the Path
	 * @return integer Returns the level of the Module if one is executed or
	 * the level of an Headmodule
	 */
	protected function lowestModuleExecutionOfPathGet($levels) {

		$maxLevel = count($levels) - 1;

		//Check if only an Headmodule gets executed or an Module, too
		if($maxLevel == ModulepathLevel::HEADMODULE) {
			return ModulepathLevel::HEADMODULE;
		}
		else if($maxLevel >= ModulepathLevel::MODULE) {
			return ModulepathLevel::MODULE;
		}
		else {
			return false;
		}
	}

	protected function rootAddToExecutionpathIfNotExists($path) {

		if(strstr($path, "root{$this->_pathDelim}") === false) {
			$path = "root{$this->_pathDelim}" . $path;
		}

		return $path;
	}
	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Saves which method is allowed to send ModuleExecution-Commands
	 *
	 * @var array
	 */
	protected $_methodAllowed = array(
		'GET' => true,
		'POST' => true,
		'SESSION' => true
	);

	protected $_usedContainer = false;

	/**
	 * Delimits the Levels of the Execution-Path. Used internally
	 *
	 * Used for parsing the Input of GET, POST and SESSION. If Interfacing
	 * with Code outside of this class, the Delimiter gets converted to the
	 * standard Slash ('/')
	 *
	 * @var string
	 */
	protected $_pathDelim = '|';

	/**
	 * The Path of the Module-Execution
	 *
	 * @var string
	 */
	protected $_executionPath = '';

	/**
	 * Used for Oldschool-section declaration: "Headmodule|Module"
	 *
	 * @var String
	 */
	protected $_subProgramPath;
}

?>
