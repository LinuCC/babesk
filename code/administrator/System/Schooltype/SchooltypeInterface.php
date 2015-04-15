<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class SchooltypeInterface extends AdminInterface {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct ($modPath, $smarty) {
		parent::__construct ($modPath, $smarty);
		$this->parentPath = $this->tplFilePath . 'header.tpl';
		$this->smarty->assign('inh_path', $this->parentPath);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function mainMenu($schooltypes) {

		$this->smarty->assign('schooltypes', $schooltypes);
		$this->smarty->display($this->tplFilePath . 'mainMenu.tpl');
	}

	public function addSchooltype() {

		$this->smarty->display($this->tplFilePath . 'addSchooltype.tpl');
	}

	public function changeSchooltype($schooltype) {

		$this->smarty->assign('schooltype', $schooltype);
		$this->smarty->display($this->tplFilePath . 'changeSchooltype.tpl');
	}

	public function deleteSchooltype($schooltype) {

		$this->smarty->assign('schooltype', $schooltype);
		$this->smarty->display($this->tplFilePath . 'deleteSchooltype.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}
?>