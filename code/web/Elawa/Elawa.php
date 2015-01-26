<?php

namespace web\Elawa;

require_once PATH_INCLUDE . '/Module.php';

class Elawa extends \Module {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->displayOverview();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::moduleTemplatePathSet();
	}

	protected function displayOverview() {

		$user = $this->_em->find('DM:SystemUsers', $_SESSION['uid']);
		if(!$user) {
			$this->_interface->dieError('Konnte die Daten nicht abrufen');
			$this->_logger->log('Error fetching userdata', 'Moderate', Null,
				json_encode(array('userId' => $_SESSION['uid'])));
		}
		$query = $this->_em->createQuery(
			'SELECT m, r, c, h FROM DM:ElawaMeeting m
			LEFT JOIN m.room r
			LEFT JOIN m.category c
			INNER JOIN m.host h
			WHERE m.visitor = :user
		');
		$query->setParameter('user', $user);
		$meetings = $query->getResult();
		$this->_smarty->assign('meetings', $meetings);
		$this->displayTpl('overview.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>