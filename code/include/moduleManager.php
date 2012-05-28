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

/**
 *
 * @author Samuel Groß; Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class ModuleManager {
	/**
	 * @param string $program_part The Part of the Program ('administrator' or 'web') as seen in modules.xml
	 */
	function __construct ($program_part) {

		$mod_xml = simplexml_load_file(PATH_INCLUDE . '/modules.xml');
		$this->programPartPath = $mod_xml->$program_part->path;
		foreach ($mod_xml->$program_part->head_module as $head_mod) {

			/**
			 * HeadModules
			 */
			$headmod_path = PATH_SITE . '/' . $this->programPartPath . sprintf('headmod_%s/%s.php', (string) $head_mod->
				name, (string) $head_mod->name);

			if (file_exists($headmod_path))
				include_once $headmod_path;

			if (class_exists((string) $head_mod->name)) {
				$headmod_classname = (string) $head_mod->name;
				$headModule = new $headmod_classname((string) $head_mod->name, (string) $head_mod->ger_name);
			}
			else {
				echo 'No file or class does exist for the HeadModule ' . $head_mod->name .
					', falling back to the StandardClass! P.S.: If you see this, then the Log-Modul isnt finished yet<br>';
				$headModule = new HeadModule((string) $head_mod->name, (string) $head_mod->ger_name);
			}
			$this->headModules [] = $headModule;

			/**
			 * Modules
			 */
			foreach ($head_mod->module as $module) {
				$include_path = sprintf(PATH_SITE . '/' . $this->programPartPath . 'headmod_%s/modules/mod_%s/%s.php',
					(string) $head_mod->name, $module->name, $module->name);
				if (!(file_exists($include_path))) {
					echo '<br>Could not load file for Module ' . $module->name;
				}
				else {
					include_once $include_path;
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

	function getAllModules () {
		
		$module_arr = array();
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				$module_arr [] = $module;
			}
		}
		if(count($module_arr) < 1)
			throw new Exception('Es sind keine Module vorhanden!');
		return $module_arr;
	}

	public function allowModules ($allowed_modules_array) {

		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				foreach ($allowed_modules_array as $mod_name) {
					if ($mod_name == $module) {
						$_SESSION['modules'][$head_mod->getName() . '|' . $module->getName()] = True; //allow module
					}
				}
			}
		}
	}

	public function allowAllModules () {
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				$_SESSION['modules'][$head_mod->getName() . '|' . $module->getName()] = True;
			}
		}
	}

	function getAllowedModules () {

		$allowedModules = array();
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				if (!isset($_SESSION['modules'][$head_mod->getName() . '|' . $module->getName()])) {
					echo('Ein Fehler ist mit Modul-Session-Variablen aufgetreten! Bitte loggen sie sich neu ein!');
					global $adminManager;
					$adminManager->userLogOut();
				}
				if ($_SESSION['modules'][$head_mod->getName() . '|' . $module->getName()])
					$allowedModules[] = $head_mod->getName() . '|' . $module->getName();
			}
		}
		return $allowedModules;
	}

	/**
	 * @return array(HeadModule)
	 */
	function getHeadModules () {
		return $this->headModules;
	}

	function getHeadModulesOfModules ($modules) {

		$head_mod_arr = array();
		$headModules = $this->getHeadModules();

		foreach ($modules as $module) {

			$mod_arr = explode('|', $module);
			$head_mod_name = $mod_arr[0];
			foreach ($head_mod_arr as $head_mod) {
				if ($head_mod->getName() == $head_mod_name)
					continue 2;
			}
			foreach ($headModules as $headModule) {
				if ($headModule->getName() == $head_mod_name) {
					$head_mod_arr[] = $headModule;
				}
			}
		}

		return $head_mod_arr;
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
	
	function getModuleDisplayName($module_name) {
		
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				if($module->getName() == $module_name)
					return $module->getDisplayName();
			}
		}
		return false;
	}

	function execute ($mod_name) {

		$name_arr = explode('|', $mod_name);

		if (count($name_arr) < 2)
			$this->executeHeadModul($name_arr[0]);

		$head_mod_name = $name_arr[0];
		$child_mod_name = $name_arr[1];

		foreach ($this->headModules as $head_module) {
			if ($head_module->getName() == $head_mod_name) {
				$head_module->executeModule($child_mod_name);
				return;
			}
		}
		die('Headmodule not found! Modulename:' . $mod_name);
	}

	public function executeHeadModul ($headmod_name) {
		foreach ($this->headModules as $head_module) {
			if ($head_module->getName() == $headmod_name) {
				$head_module->execute();
				return;
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