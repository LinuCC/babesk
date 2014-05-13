<?php

namespace administrator\System\User\DisplayAll\Multiselection;

require_once PATH_ADMIN . '/headmod_System/modules/mod_User/DisplayAll/Multiselection/Multiselection.php';

class ActionsGet extends \Multiselection {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->render();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
	}

	protected function render() {

		//Require all ORM-Entities allowing the plugins to use them
		require_all(PATH_INCLUDE . '/orm-entities/');
		$this->_smarty->assign('doctrine', $this->_entityManager);
		$this->displayTpl('actions/base.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>