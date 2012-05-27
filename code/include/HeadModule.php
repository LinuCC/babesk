<?php

class HeadModule {
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $smartyModPath;
	private $adminModPath;
	private $webModPath;
	private $modules;
	private $name;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name) {

		$this->name = $name;
		$this->webModPath = PATH_WEB . '';
		$this->adminModPath = PATH_ADMIN . '/headmod_babesk/modules/';
		$this->smartyModPath = PATH_SMARTY . '/templates/';
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	public function getModules() {
		return $this->modules;
	}
	
	public function getName() {
		return $this->name;
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * @param Module $module
	 * @throws Exception if Module is already existing
	 */
	public function addModule ($new_module) {
		
		if (count($this->modules) != 0) {
			foreach ($this->modules as $module) {
				if ($new_module->getName() == $module->getName())
					throw new Exception('Module already existing: ' . $new_module->getName());
			}
		}
		$this->modules[] = $new_module;
	}
	
	/**
	 * @param string $mod_name
	 */
	public function executeModule($mod_name) {
		
		foreach($this->modules as $module) {
			
			if($module->getName() == $mod_name) {
				if (!$_SESSION['modules'][$this->name . '|' . $module->getName()])
					die('Module forbidden');
				
				$module->execute();
				return;
			}
		}
		die('ChildModule not found!');
	}
	
}

?>