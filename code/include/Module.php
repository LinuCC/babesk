<?php

abstract class Module {

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	protected $name;
	protected $relPath;
	protected $displayName;
	protected $executablePath;

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
	 * Executes a Submodule by calling a Method
	 *
	 * The name of the called Method begins with submodule, goes on with the
	 * modules name and ends with Execute. For Example submoduleUserExecute()
	 *
	 * @param  String $path The Path to the Submodule, beginning from the
	 *                      moduleroot
	 * @return ???          Returns the value that the Submodule returns
	 */
	protected function submoduleExecute($path) {

		$submodule = $this->_acl->moduleGet($path);
		if($submodule) {
			$methodName = 'submodule' . $submodule->getName() . 'Execute';
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
}

?>
