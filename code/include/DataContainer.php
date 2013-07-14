<?php

/**
 * This class contains Data used by the modules. It is intended to solve the problem of global data
 * (like the smarty-variable) and is a "databridge" between the main-Class of the Program which stores
 * all the Information (here Administrator.php) and the modules.
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class DataContainer {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct ($smarty, $interface, $acl = NULL) {
		$this->_smarty = $smarty;
		$this->_interface = $interface;
		$this->_acl = $acl;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function getSmarty() {
		return $this->_smarty;
	}

	public function setSmarty($smarty) {
		$this->_smarty = $smarty;
	}

	public function setInterface($interface) {
		$this->_interface = $interface;
	}

	public function getInterface() {
		return $this->_interface;
	}

	public function getDatabase() {
		return $this->_db;
	}

	public function setDatabase($db) {
		$this->_db = $db;
	}

	public function getAcl() {
		return $this->_acl;
	}

	public function setAcl($acl) {
		$this->_acl = $acl;
	}

	public function getModuleExecutionPath() {
		return $this->_moduleExecutionPath;
	}

	public function setModuleExecutionPath($moduleExecutionPath) {
		$this->_moduleExecutionPath = $moduleExecutionPath;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	/**
	 * Needed to use Smarty, an external Templating-Program for better
	 * separation between Programcode and Displaying Code
	 */
	protected $_smarty;
	protected $_interface;
	protected $_db;

	/**
	 * The AccessControlLayer
	 *
	 * @var Acl
	 */
	protected $_acl;

	/**
	 * The Module-Executionpath
	 *
	 * @var String
	 */
	protected $_moduleExecutionPath;
}

?>
