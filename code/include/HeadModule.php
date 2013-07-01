<?php

class HeadModule {
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $modules;
	private $name;
	private $displayName;
	private $headmod_menu;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($name, $display_name, $headmod_menu) {

		$this->name = $name;
		$this->displayName = $display_name;
		$this->headmod_menu = $headmod_menu;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	public function getModules() {
		return $this->modules;
	}

	public function getName() {
		return $this->name;
	}

	public function getDisplayName() {
		return $this->displayName;
	}

	public function getHeadmodMenu() {
		return $this->headmod_menu;
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
	public function executeModule($mod_name, $dataContainer) {

		if(!count($this->modules)) {
			throw new Exception('No Modules found!');
		}
		foreach($this->modules as $module) {

			if($module->getName() == $mod_name) {
				if (!$_SESSION['modules'][$this->name . '|' . $module->getName()]) {
					die('Module forbidden');
				}

				$module->execute($dataContainer);
				return;
			}
		}
		throw new ModuleNotFoundException('ChildModule not found: "' . $mod_name . '"!');
	}

	public function execute($dataContainer) {
		die('No entrypoint of this Headmodule given');
	}

}

?>
