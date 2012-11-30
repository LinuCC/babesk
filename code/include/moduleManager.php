<?php
/**
 * Manages the modules of the administrator frontend.
 *
 * The available modules are extracted from modules.php and the appropriate path is calculated.
 * The ModuleManager also checks the users rights before including a module.
 */

///@todo comments!

require_once 'Module.php';
require_once 'HeadModule.php';
require_once 'exception_def.php';
require_once 'GeneralInterface.php';
require_once 'DataContainer.php';

/**
 *
 * @author Samuel Groß; Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class ModuleManager {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * @param string $program_part The Part of the Program ('administrator' or 'web') as seen in modules.xml
	 * @param GeneralInterface $general_interface The Class or a from GeneralInterface inheriting class, used for Useroutput
	 */
	function __construct ($program_part = NULL, $general_interface = NULL) {

		$this->moduleXMLPath = PATH_INCLUDE . '/modules.xml';

		if ($general_interface != NULL) {
			$this->generalInterface = $general_interface;
		}
		else {
			$this->generalInterface = new GeneralInterface();
		}

		if (isset($program_part)) {
			$this->parseModuleXML($program_part);
		}

	}

	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function setModuleXMLPath ($path) {
		$this->moduleXMLPath = $path;
	}

	public function setDataContainer ($dC) {
		$this->dataContainer = $dC;
	}

	public function getDataContainer () {
		return $this->dataContainer;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function getAllModules () {

		$module_arr = array();
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				$module_arr[] = $module;
			}
		}
		if (count($module_arr) < 1)
			throw new Exception('Es sind keine Module vorhanden!');
		return $module_arr;
	}

	public function allowModules ($allowed_modules_array) {

		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				foreach ($allowed_modules_array as $mod_name) {
					if ($mod_name == $module->getName()) {
						$_SESSION['modules'][$head_mod->getName() . '|' . $module->getName()] = True; //allow module
						continue 2;
					}
				}
				$_SESSION['modules'][$head_mod->getName() . '|' . $module->getName()] = False; //disallow module

			}
		}
	}

	public function allowAllModules () {
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			if (count($modules) < 1)
				return;
			foreach ($modules as $module) {
				$_SESSION['modules'][$head_mod->getName() . '|' . $module->getName()] = True;
			}
		}
	}

	public function getAllowedModules () {

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
	public function getHeadModules () {
		return $this->headModules;
	}

	public function getHeadModulesOfModules ($modules) {

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
	public function getModuleIdentifier ($module_name) {

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

	public function getModuleDisplayNames () {

		$module_names = array();
		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				$module_names[$head_mod->getName() . '|' . $module->getName()] = $module->getDisplayName();
			}
		}
		return $module_names;
	}

	public function getModuleDisplayName ($module_name) {

		foreach ($this->headModules as $head_mod) {
			$modules = $head_mod->getModules();
			foreach ($modules as $module) {
				if ($module->getName() == $module_name)
					return $module->getDisplayName();
			}
		}
		return false;
	}

	/**
	 * @param Class $dataContainer a Class which contains data for the Module to use. if no dataContainer is used,
	 * just use the value false
	 */
	public function execute ($mod_name) {

		$name_arr = explode('|', $mod_name);

		if (count($name_arr) < 2) {
			$this->executeHeadModul($name_arr[0]);
			return;
		}

		$head_mod_name = $name_arr[0];
		$child_mod_name = $name_arr[1];

		foreach ($this->headModules as $head_module) {
			if ($head_module->getName() == $head_mod_name) {
				$head_module->executeModule($child_mod_name, $this->dataContainer);
				return;
			}
		}
		throw new HeadModuleNotFoundException('Headmodule not found! Modulename:' . $mod_name);
	}

	public function executeHeadModul ($headmod_name) {
		if (!count ($this->headModules)) {
			$this->generalInterface->dieError ('No HeadModules active! Check modules.xml');
		}
		foreach ($this->headModules as $head_module) {
			if ($head_module->getName() == $headmod_name) {
				$head_module->execute($this, $this->dataContainer);
				return;
			}
		}
	}

	public function parseModuleXML ($program_part) {

		/**
		 * @todo: Use functional Errorhandling in this function
		 */
		$mod_xml = simplexml_load_file($this->moduleXMLPath);

		if (!$mod_xml) {
			die("Could not parse the ModuleXML");
		}
		$this->programPartPath = (string) $mod_xml->$program_part->path;

		foreach ($mod_xml->$program_part->head_module as $head_mod) {

			if (!isset($head_mod->active) || $head_mod->active != true) {
				echo 'A headmodule is not active; it will not be used.';
				continue;
			}
			$headModule = $this->extractHeadModule($head_mod);

			foreach ($head_mod->module as $module) {

				if (!isset($module->active) || $module->active != true) {
					echo 'A module is not active; it will not be used.';
					continue;
				}

				$this->extractModule($module, $headModule);
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	private function extractHeadModule ($head_mod) {

		$headmod_path = PATH_CODE . '/' . $this->programPartPath . sprintf('headmod_%s/%s.php', (string) $head_mod->name,
			(string) $head_mod->name);
		$headModule = $this->createHeadModule($headmod_path, $head_mod);
		$this->headModules [] = $headModule;
		return $headModule;
	}

	private function createHeadModule ($headmod_path, $head_mod) {

		if (file_exists($headmod_path))
			include_once $headmod_path;

		if (class_exists((string) $head_mod->name)) {
			$headmod_classname = (string) $head_mod->name;
			$headModule = new $headmod_classname((string) $head_mod->name, (string) $head_mod->ger_name);
		}
		else {
			$this->generalInterface->showError('No file or class does exist for the HeadModule ' . $head_mod->name .
				', falling back to the StandardClass! If you see this, then the Log-Modul isnt finished yet<br>');
			$headModule = new HeadModule((string) $head_mod->name, (string) $head_mod->ger_name);
		}
		return $headModule;
	}

	private function extractModule ($module, $headModule) {

		$include_path = sprintf(PATH_CODE . '/' . $this->programPartPath . 'headmod_%s/modules/mod_%s/%s.php', (string)
			$headModule->getName(), $module->name, $module->name);

		$this->createModule($include_path, $module, $headModule);

	}

	private function createModule ($include_path, $module, $headModule) {

		if (!(file_exists($include_path))) {
			$this->generalInterface->showError('Could not load file for Module ' . $module->name);
		}
		else {
			include_once $include_path;
			if (!class_exists($classname = (string) $module->name)) {
				$this->generalInterface->showError('<br>Could not load the class ' . $classname . '!');
			}
			else {
				$new_module = new $classname((string) $module->name, (string) $module->ger_name, sprintf(
					'/headmod_%s/modules/mod_%s/', (string) $headModule->getName(), $module->name));
				$headModule->addModule($new_module);
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $headModules;
	private $programPartPath;
	private $moduleXMLPath;
	private $generalInterface;
	private $dataContainer;
}

?>