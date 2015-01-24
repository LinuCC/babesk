<?php

namespace administrator\Elawa\Meetings\ChangeDisableds;

require_once PATH_ADMIN . '/Elawa/Meetings/Meetings.php';

class ChangeDisableds extends \administrator\Elawa\Meetings\Meetings {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		if(isset($_POST['hostId'])) {
			$host = $this->_em->getReference(
				'DM:SystemUsers', $_POST['hostId']
			);
			$this->sendHostMeetingData($host);
		}
		else if(isset($_POST['meetingId'])) {
			$meeting = $this->_em->getReference(
				'DM:ElawaMeeting', $_POST['meetingId']
			);
			$isDisabled = $_POST['isDisabled'] == 'true';
			$meeting->setIsDisabled($isDisabled);
			$this->_em->persist($meeting);
			$this->_em->flush();
		}
		else {
			$hosts = $this->getHosts();
			$this->_smarty->assign('hosts', $hosts);
			$this->displayTpl('changeDisableds.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function getHosts() {

		$query = $this->_em->createQuery(
			'SELECT u, m FROM DM:SystemUsers u
			INNER JOIN u.elawaMeetingsHosting m
		');
		$users = $query->getResult();
		if($users && count($users)) {
			return $users;
		}
		else {
			$this->_interface->dieError(
				'Es gibt keine Benutzer, die Sprechzeiten haben.'
			);
		}
	}

	protected function sendHostMeetingData($host) {

		$query = $this->_em->createQuery(
			'SELECT m, c FROM DM:ElawaMeeting m
			INNER JOIN m.category c
			WHERE m.host = :host
			ORDER BY m.time ASC
		');
		$query->setParameter('host', $host);
		$meetings = $query->getResult();
		if($meetings && count($meetings)) {
			$meetingAr = array();
			foreach($meetings as $meeting) {
				$meetingAr[] = array(
					'id' => $meeting->getId(),
					'time' => $meeting->getTime()->format('H:i:s'),
					'length' => $meeting->getLength()->format('H:i:s'),
					'category' => $meeting->getCategory()->getName(),
					'isDisabled' => $meeting->getIsDisabled()
				);
			}
			die(json_encode($meetingAr));
		}
		else {
			http_response_code(204);
			die();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

}