<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class WebHomepageSettingsInterface extends AdminInterface {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////
	public function __construct ($modPath, $smarty) {
		parent::__construct($modPath, $smarty);
		$this->parentPath = $this->tplFilePath . 'header.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
	}

	public function mainMenu () {
		$this->smarty->display ($this->tplFilePath . 'mainMenu.tpl');
	}

	public function redirect ($delay,$target) {
		$this->smarty->assign('delay',$delay);
		$this->smarty->assign('target',$target);
		$this->smarty->display ($this->tplFilePath . 'redirect.tpl');
	}
	
	public function helptext ($helptext) {
		$this->smarty->assign('helptext',$helptext);
		$this->smarty->display ($this->tplFilePath . 'helptext.tpl');
	}

    public function maintenance ($maintenance) {
        $this->smarty->assign('maintenance',$maintenance);
        $this->smarty->display ($this->tplFilePath . 'maintenance.tpl');
    }
	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}


?>