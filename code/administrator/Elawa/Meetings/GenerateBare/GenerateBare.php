<?php

namespace administrator\Elawa\Meetings\GenerateBare;

require_once PATH_ADMIN . '/Elawa/Meetings/Meetings.php';

class GenerateBare extends \administrator\Elawa\Meetings\Meetings {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		$numDeleted = $this->clearMeetings();
		$numCreated = $this->generate();
		$this->_interface->dieSuccess(
			'Die Sprechzeiten wurden erfolgreich erstellt. <br>' .
			'Es wurden ' . $numDeleted . ' alte Sprechzeiten gelöscht und ' .
			$numCreated . ' neue erstellt.'
		);
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Removes all existing meetings.
	 * @return int    The number of meetings deleted
	 */
	protected function clearMeetings() {

		$query = $this->_em->createQuery(
			'DELETE FROM DM:ElawaMeeting m
		');
		$numDeleted = $query->execute();
		return $numDeleted;
	}

	protected function generate() {

		$this->_noVisitor = $this->_em->getReference('DM:SystemUsers', 0);
		$this->_noRoom = $this->_em->getReference('DM:SystemRoom', 0);
		$users = $this->getHostGroupUsers();
		$defaultTimes = $this->getDefaultMeetingTimes();
		$countCreated = 0;
		foreach($users as $user) {
			foreach($defaultTimes as $defaultTime) {
				$this->persistNewMeeting($user, $defaultTime);
				$countCreated++;
			}
		}
		$this->_em->flush();
		return $countCreated;
	}

	/**
	 * Fetches and returns the group of the users that are hosts in meetings
	 * @return \Babesk\ORM\SystemGroups
	 */
	protected function getHostGroup() {

		$gsEntry = $this->_em->getRepository('DM:SystemGlobalSettings')
			->findOneByName('elawaHostGroupId');
		if($gsEntry) {
			$group = $this->_em->getRepository('DM:SystemGroups')
				->findOneById(intval($gsEntry->getValue()));
			if($group) {
				return $group;
			}
			else {
				$this->_interface->dieError(
					'Hostgroup konnte nicht gefunden werden!'
				);
			}
		}
		else {
			$this->_interface->dieError('Keine Hostgroup definiert!');
		}
	}

	protected function getHostGroupUsers() {

		$group = $this->getHostGroup();
		$query = $this->_em->createQuery(
			'SELECT u, g, r
			FROM DM:SystemUsers u
			INNER JOIN u.groups g
			LEFT JOIN u.elawaDefaultMeetingRooms r
			WHERE g = :group
		');
		$query->setParameter('group', $group);
		$users = $query->getResult();
		if($users && count($users)) {
			return $users;
		}
		else {
			$this->_interface->dieMsg(
				'Keine Benutzer für die Hostgroup gefunden.'
			);
		}
	}

	protected function getDefaultMeetingTimes() {

		$times = $this->_em->getRepository('DM:ElawaDefaultMeetingTime')
			->findAll();
		if($times) {
			return $times;
		}
		else {
			$this->_interface->dieError('Keine Standard-Zeiten definiert!');
		}
	}

	protected function persistNewMeeting($user, $defaultTime) {

		$room = $this->_noRoom;
		$defaultRooms = $user->getElawaDefaultMeetingRooms();
		if($defaultRooms && count($defaultRooms)) {
			foreach($defaultRooms as $defRoom) {
				if($defRoom->getCategory() == $defaultTime->getCategory()) {
					$room = $defRoom->getRoom();
				}
			}
		}
		$meeting = new \Babesk\ORM\ElawaMeeting();
		$meeting->setVisitor($this->_noVisitor)
			->setHost($user)
			->setTime($defaultTime->getTime())
			->setLength($defaultTime->getLength())
			->setCategory($defaultTime->getCategory())
			->setRoom($room)
			->setIsDisabled(false);
		$this->_em->persist($meeting);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Represents the absence of a visitor
	 */
	protected $_noVisitor;

	/**
	 * Represents the absence of a room
	 */
	protected $_noRoom;
}

?>