<?php

namespace administrator\Elawa;

require_once 'Elawa.php';

class MainMenu extends Elawa {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->display();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
		$stuff = $this->_em->getRepository('DM:ElawaMeeting')->findAll();
	}

	private function display() {

		$settingsRep = $this->_em->getRepository('DM:SystemGlobalSettings');
		$hostGroupId = $settingsRep->findOneByName('elawaHostGroupId');
		$selectionsEnabledObj = $settingsRep->findOneByName(
			'elawaSelectionsEnabled'
		);
		if($selectionsEnabledObj) {
			$selectionsEnabled = $selectionsEnabledObj->getValue() != "0";
		}
		else {
			$selectionsEnabled = false;
		}
		$group = $this->_em->find('DM:SystemGroups', $hostGroupId->getValue());
		$this->_smarty->assign('selectionsEnabled', $selectionsEnabled);
		$this->_smarty->assign('group', $group);
		$this->displayTpl('mainMenu.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>