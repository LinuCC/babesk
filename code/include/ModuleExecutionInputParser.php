<?php

require_once PATH_INCLUDE . '/ModuleExecutionCommand.php';

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
			$this->_executionCommand = new ModuleExecutionCommand($manualPath);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Sets the Subprogrampath. Needed when using the old section-style
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

				$exePath = str_replace('|', '/',
					$this->rootAddToExecutionpathIfNotExists($path));
				$this->_executionCommand = new ModuleExecutionCommand(
					$exePath);
			}
			else {
				return false;
			}

		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	public function executionCommandGet() {

		return $this->_executionCommand;
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
	 * Used for Oldschool-section declaration: "Headmodule|Module"
	 *
	 * @var String
	 */
	protected $_subProgramPath;

	protected $_executionCommand;
}

?>
