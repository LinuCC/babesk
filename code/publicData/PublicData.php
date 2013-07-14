<?php

require_once "../include/path.php";
require_once 'PublicDataInterface.php';
require_once PATH_INCLUDE . '/DataContainer.php';
require_once PATH_INCLUDE . '/Acl.php';

/**
 * This Class organizes the Sub-program "publicData"
 * @author Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class PublicData {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct () {
		$this->environmentInit ();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	public function moduleExecute ($moduleName) {

		try {
			$this->_acl->accessControlInit($_SESSION['uid']);
			$this->_acl->moduleExecute($moduleName, $this->_dataContainer);

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte Modul nicht ausführen');
		}
	}

	public function publicDataEntrypoint () {
		if (isset ($_GET ['section'])) {
			$this->moduleExecute ($_GET ['section']);
		}
		else {
			$this->_interface->dieError ('No Module requested; Aborting');
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	private function environmentInit () {
		$this->phpIniSet();
		$this->sessionInit ();
		$this->attributesInit ();
		//if this value is not set, the modules will not execute
		define('_AEXEC', 1);
		error_reporting(E_ALL);
	}

	private function attributesInit () {
		$this->_interface = new PublicDataInterface ();
		// $this->_moduleManager = new ModuleManager ('publicData', $this->_interface);
		// $this->_moduleManager->setDataContainer ($this->_dataContainer);
		// $this->_moduleManager->allowAllModules ();
		$this->_acl = new Acl();
		$this->_acl->setSubprogramPath('root/PublicData');
		$this->_dataContainer = new DataContainer (
			$this->_interface->getSmarty (),
			$this->_interface,
			$this->_acl);
	}

	private function sessionInit () {
		session_name('sid');
		session_start();
	}

	private function phpIniSet () {
		ini_set('display_errors', 1);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 0);
		ini_set("default_charset", "utf-8");
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_interface;
	private $_dataContainer;
}

?>
