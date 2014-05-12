<?php

namespace administrator\System\User\DisplayAll\Multiselection;

require_once PATH_ADMIN . '/headmod_System/modules/mod_User/DisplayAll/Multiselection/Multiselection.php';

class ActionExecute extends \Multiselection {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(!empty($_POST['actionName'])) {
			$this->actionExecute();
		}
		else {
			die(json_encode(array('value' => 'error',
				'message' => 'Kein Aktionsname gegeben!')));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
	}

	protected function actionExecute() {

		$name = $_POST['actionName'];
		//Be safe, dont allow execution in other directories
		str_replace('/', '', $name);
		$class = 'administrator\\System\\User\\DisplayAll\\Multiselection\\' .
			'Actions\\' . $name;
		if((include __DIR__ . "/ActionHandlers/$name.php") === 1) {
			if(class_exists($class)
				) {
				$action = new $class($this->_dataContainer);
				$action->actionExecute($_POST);
			}
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>