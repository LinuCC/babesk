<?php
/**
 * Manages the modules of the administrator frontend.
 *
 * The available modules are extracted from modules.php and the appropriate path is calculated.
 * The ModuleManager also checks the users rights before including a module.
 */

///@todo comments!

require_once PATH_ADMIN . '/modules.php';
require_once 'Module.php';
require_once 'HeadModule.php';

class ModuleManager {

	function __construct () {

		//Administrator-Modules
		$admin_mod_xml = simplexml_load_file(PATH_ADMIN . '/modules.xml');

		foreach ($admin_mod_xml->head_module as $head_mod) {

			$headModule = new HeadModule((string) $head_mod->name);
			$this->headModules [] = $headModule;
			foreach ($head_mod->module as $module) {
				
				$new_module = new Module((string) $module->name, (string) $module->ger_name, 
						sprintf(PATH_ADMIN . '/headmod_%s/modules/mod_%s/', (string) $head_mod->name, $module->name));
				$headModule->addModule($new_module);
			}
		}

		//foreach($admin_mod_xml->babesk->module as $module) {
		//	$this->modules [] = new Module((string)$module->name, (string) $module->ger_name, 'blubb');
		//$this->modules[(string)$module->name] = (string) $module->ger_name;
		//}
		// 		echo '<br>';
		// 		echo '<br>';
		// 		var_dump($this->modules);
		// 		echo '<br>';
		// 		echo '<br>';

		//$admin_mod_xml->babesk->admin->name;

		//$this->modules = $modules;

	}

	function getModules () {
		return $this->modules;
	}

	function getAllowedModules () {
		
		$allowedModules = array();
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				if ($_SESSION['modules'][$module->getName()])
					$allowedModules[] = $module->getName();
			}
		}
		return $allowedModules;
	}

	/**
	 * returns the Modulename of the given ModuleID
	 * Enter description here ...
	 * @return string the Modulename
	 * @throws BadMethodCallException if Module is not existing
	 */
	function getModuleName ($id) {
		if (!$mod_name = $this->modules[$id]) {
			throw new BadMethodCallException('There is no module with this ID! ' . $id);
		}
		return $mod_name;
	}

	function executeWeb ($mod_name) {
		$mod_path = "modules/mod_" . $mod_name . "/" . $mod_name . ".php";
		foreach ($this->modules as $module) {
			if ($module == $mod_name AND file_exists($mod_path)) {
				require $mod_path;
				return;
			}
			//the module was not found
			elseif ($module == end($this->modules)) {
				//the loop finished so the module was not found
				echo MODULE_NOT_FOUND;
			}
		}
	}

	function execute ($mod_name) {

		$name_arr = explode('|', $mod_name);
		
		$head_mod_name = $name_arr [0];
		$child_mod_name = $name_arr [1];
		
		foreach($this->headModules as $head_module) {
			if($head_module->getName() == $head_mod_name) {
				$head_module->executeModule($child_mod_name);
				return;
			}
		}
		die('Headmodule not found!');
		
		
		die(var_dump($name_arr));

		$mod_path = "modules/mod_" . $mod_name . "/" . $mod_name . ".php";
		foreach ($this->modules as $module) {
			if ($module == $mod_name AND file_exists($mod_path)) {
				//check for correct rights
				if ($_SESSION['modules'][$mod_name]) {
					require $mod_path;
					return;
				}
				else {
					echo MODULE_FORBIDDEN;
				}
			}
			//the module was not found
			elseif ($module == end($this->modules)) {
				//the loop finished so the module was not found
				echo MODULE_NOT_FOUND;
			}
		}
	}
	/**
	 * $modules is an array, the key being the "id's" of the modules and the values the names
	 * Enter description here ...
	 * @var array
	 */
	private $headModules;
}

?>