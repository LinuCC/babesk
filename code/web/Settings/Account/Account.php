<?php

namespace web\Settings\Account;

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/Settings/Settings.php';

class Account extends \Settings {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$lockAccount = filter_input(INPUT_GET, 'lockAccount');
		if($lockAccount == 'lockAccount') {
			$user = $this->_em->getReference(
				'DM:SystemUsers', $_SESSION['uid']
			);
			if($user) {
				$this->lockUserAccount($user);
				header('Location: index.php?action=logout');
			}
			else {
				$this->_logger->logO('Could not find the user to lock', [
					'sev' => 'notice', 'moreJson' => ['user' => $user->getId()]
				]);
				$this->_interface->dieError('Benutzer nicht gefunden.');
			}
		}
		else if($lockAccount == 'confirm') {
			$this->displayTpl('lockConfirmation.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::initSmartyVariables();
		$this->_smarty->assign(
			'inh_path', PATH_SMARTY_TPL . '/web/baseLayout.tpl'
		);
	}

	protected function lockUserAccount($user) {

		$user->setLocked(true);
		$this->_em->persist($user);
		$this->_em->flush();
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}

?>