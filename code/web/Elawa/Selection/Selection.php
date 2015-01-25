<?php

namespace web\Elawa\Selection;

require_once PATH_WEB . '/Elawa/Elawa.php';

class Selection extends \web\Elawa\Elawa {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		if(isset($_POST['meetingId'])) {
			$this->registerSelection($_POST['meetingId']);
		}
		else {
			$host = $this->_em->getReference('DM:SystemUsers', 4);
			$this->displaySelection($host);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function displaySelection($host) {

		$query = $this->_em->createQuery(
			'SELECT m, r, c FROM DM:ElawaMeeting m
			LEFT JOIN m.category c
			LEFT JOIN m.room r
			WHERE m.host = :host
			ORDER BY m.time ASC
		');
		$query->setParameter('host', $host);
		$meetings = $query->getResult();
		$this->_smarty->assign('meetings', $meetings);
		$this->_smarty->assign('host', $host);
		$this->displayTpl('selection.tpl');
	}

	protected function registerSelection($meetingId) {

		$meeting = $this->_em->getReference('DM:ElawaMeeting', $meetingId);
		$query = $this->_em->createQuery(
			'SELECT m, h FROM DM:ElawaMeeting m
			LEFT JOIN m.host h
			WHERE m = :meeting
		');
		$query->setParameter('meeting', $meeting);
		$meeting = $query->getOneOrNullResult();
		$this->_interface->moduleBacklink('web|Elawa|Selection');
		if(!$meeting) {
			$this->_interface->dieError('Diese Sprechzeit existiert nicht!');
		}
		$user = $this->_em->getReference('DM:SystemUsers', $_SESSION['uid']);
		$this->checkRegisterSelectionValid($meeting, $user);

		$meeting->setVisitor($user);
		$this->_em->persist($meeting);
		$this->_em->flush();
		$this->_interface->dieSuccess(
			'Die Sprechzeit wurde erfolgreich angemeldet'
		);
	}

	protected function checkRegisterSelectionValid($meeting, $user) {

		$countQuery = $this->_em->createQuery(
			'SELECT COUNT(m.id) FROM DM:ElawaMeeting m
			INNER JOIN m.visitor v
			INNER JOIN m.host h
			WHERE v = :visitor AND h = :host
		');
		$countQuery->setParameter('visitor', $user);
		$countQuery->setParameter('host', $meeting->getHost());
		$count = $countQuery->getSingleScalarResult();
		if($count) {
			$this->_interface->dieError(
				'Sie sind bereits bei dieser Person angemeldet!'
			);
		}
		if($meeting->getIsDisabled()) {
			$this->_interface->dieError('Diese Sprechzeit ist deaktiviert!');
		}
		if($meeting->getVisitor()->getId() != 0) {
			$this->_interface->dieError(
				'Diese Sprechzeit ist leider schon vergeben. ' .
				'Da war wohl jemand schneller.'
			);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>