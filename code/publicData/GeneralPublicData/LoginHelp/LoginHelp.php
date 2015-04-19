<?php

require_once 'LoginHelpInterface.php';
require_once PATH_ACCESS . '/GlobalSettingsManager.php';
require_once PATH_INCLUDE . '/Module.php';

class LoginHelp extends Module {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->smartyPath = PATH_SMARTY_TPL . '/web' . $path;
	}
	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////
	public function execute ($dataContainer) {
		$this->entry ($dataContainer);
		$this->helptextShow ();
	}
	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////
	protected function entry ($dataContainer) {
		$this->_interface = new LoginHelpInterface ($dataContainer->getSmarty (), $this->relPath);
		self::$_globalSettingsManager = new GlobalSettingsManager ();
	}

	protected function helptextFetch () {
		try {
			$txt = self::$_globalSettingsManager->valueGet (GlobalSettings::WEBLOGIN_HELPTEXT);
		} catch (Exception $e) {
			$this->_interface->dieMsg ('Konnte den Hilfetext nicht anzeigen');
		}
		return $txt;
	}

	protected function helptextShow () {
		$txt = $this->helptextFetch ();
		$this->_interface->helptextShow ($txt);
	}
	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_globalSettingsManager;
}

?>
