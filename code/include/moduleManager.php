<?php
/**
 * Manages the modules of the administrator frontend.
 *
 * The available modules are extracted from modules.php and the appropriate path is calculated.
 * The ModuleManager also checks the users rights before including a module.
 */

///@todo comments!

//require_once PATH_ADMIN . '/modules.php';
require_once 'Module.php';
require_once 'HeadModule.php';

class ModuleManager {
	/**
	 * @param string $program_part The Part of the Program ('administrator' or 'web') as seen in modules.xml
	 */
	function __construct ($program_part) {

		$mod_xml = simplexml_load_file(PATH_INCLUDE . '/modules.xml');
		$this->programPartPath = $mod_xml->$program_part->path;

		foreach ($mod_xml->$program_part->head_module as $head_mod) {

			$headModule = new HeadModule((string) $head_mod->name);
			$this->headModules [] = $headModule;
			foreach ($head_mod->module as $module) {
				$include_path = sprintf(PATH_SITE . '/' . $this->programPartPath . 'headmod_%s/modules/mod_%s/%s.php',
					(string) $head_mod->name, $module->name, $module->name);
				if (!(include_once $include_path)) {
					echo '<br>Could not load file for Module ' . $module->name;
				}
				else {
					if (!class_exists($classname = (string) $module->name)) {
						echo '<br>Could not load the class ' . $classname . '!<br>';
					}
					else {
						$new_module = new $classname((string) $module->name, (string) $module->ger_name, sprintf(
							'/headmod_%s/modules/mod_%s/', (string) $head_mod->name, $module->name));
						$headModule->addModule($new_module);
					}
				}
			}
		}
	}

	function getModules () {
		return $this->headModules;
	}

	public function allowModules ($allowed_modules_array) {

		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				foreach ($allowed_modules_array as $mod_name) {
					if ($mod_name == $module) {
						$_SESSION['modules'][$module->getName()] = True; //allow module
					}
				}
			}
		}
	}

	public function allowAllModules () {
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				$_SESSION['modules'][$module->getName()] = True;
			}
		}
	}

	function getAllowedModules () {

		$allowedModules = array();
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				if ($_SESSION['modules'][$module->getName()])
					$allowedModules[] = $head_mod->getName() . '|' . $module->getName();
			}
		}
		return $allowedModules;
	}

	/**
	 * returns something like 'babesk|Admin'
	 */
	function getModuleIdentifier ($module_name) {

		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				if ($head_mod->getName() . '|' . $module->getName() == $module_name) {
					return $head_mod->getName() . '|' . $module->getName();
				}
			}
		}
		throw new InvalidArgumentException('There is no modul with this name:' . $module_name);
	}

	function getModuleDisplayNames () {

		$module_names = array();
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				$module_names[$head_mod->getName() . '|' . $module->getName()] = $module->getDisplayName();
			}
		}
		return $module_names;
	}

	function execute ($mod_name) {

		$name_arr = explode('|', $mod_name);

		if (count($name_arr) < 2)
			die('Error reading GET-selection-settings, too few arguments. Modulename:' . $mod_name);

		$head_mod_name = $name_arr[0];
		$child_mod_name = $name_arr[1];

		foreach ($this->headModules as $head_module) {
			if ($head_module->getName() == $head_mod_name) {
				$head_module->executeModule($child_mod_name);
				return;
			}
		}
		die('Headmodule not found! Modulename:' . $mod_name);

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

	private $programPartPath;
}

?>