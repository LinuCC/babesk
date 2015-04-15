<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/Gnissel/Gnissel.php';

class GChangeCard extends Gnissel {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'changeCard':
					$this->changeCard();
					break;
				default:
					$this->_interface->dieError('Unknown action value');
					break;
			}
		}
		else {
			if('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['username'])) {
				$this->changeCardShow();
			}
			else{
				$this->displayTpl('getUsername.tpl');
			}
		}

	}

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->initSmartyVariables();
	}

	protected function changeCardShow() {

		$userRepo = $this->_em->getRepository('DM:SystemUsers');
		$user = $userRepo->findOneByUsername($_POST['username']);
		$grade = $userRepo->getActiveGradeByUser($user);
		if($user) {
			$this->_smarty->assign('uid', $user->getId());
			$this->_smarty->assign('name', $user->getName());
			$this->_smarty->assign('forename', $user->getForename());
		}
		else {
			$this->_interface->dieError(
				"Der Benutzer '$_POST[username]' wurde nicht gefunden!"
			);
		}
		if($grade) {
			$this->_smarty->assign(
				'class', $grade->getGradelevel() . $grade->getLabel()
			);
		}
		else {
			$this->_smarty->assign('class', '---');
		}
		$this->displayTpl('changeCard.tpl');
	}

	/**
	 * Changes the users cardnumber based on the input
	 */
	protected function changeCard() {

		$userRepo = $this->_em->getRepository('DM:SystemUsers');
		$user = $userRepo->findOneById($_POST['uid']);
		if(!$user) {
			$this->_interface->dieError(
				'Der Benutzer wurde nicht gefunden!'
			);
		}
		$cards = $user->getCards();
		if(count($cards) == 1) {
			$this->_interface->backlink('administrator|Gnissel|GChangeCard');
			$existingCard = $cards[0];
			$oldCardnumber = $existingCard->getCardnumber();
			$newCardnumber = $_POST['newCard'];
			$this->changeCardCheckInput($oldCardnumber, $newCardnumber);
			$existingCard->setCardnumber($newCardnumber);
			$this->_em->persist($existingCard);
			$this->_em->flush();
			$this->_interface->dieSuccess(
				"Die Kartennummer wurde erfolgreich von '$oldCardnumber' auf" .
				" '$newCardnumber' geändert."
			);
		}
		else if(count($cards) == 0) {
			$this->_interface->dieError(
				'Der Benutzer hat noch keine Karte, die verändert werden kann.'
			);
		}
		else {
			$this->_logger->log('Error changing cardnumber. User has ' .
				'multiple cards', 'Notice', NULL,
				json_encode(array('uid' => $POST['uid'])));
			$this->_interface->dieError('Der Benutzer hat mehrere Karten! ' .
				'Kann die Karte nicht wechseln.');
		}
	}

	protected function changeCardCheckInput($oldCardnumber, $newCardnumber) {

		try {
			inputcheck($newCardnumber, 'card_id', 'Kartennummer');
		} catch (WrongInputException $e) {
			$this->_interface->dieError(
				"Die Kartennummer '$oldCardnumber' wurde nicht korrekt " .
				'eingegeben.'
			);
		}
		if($oldCardnumber == $newCardnumber) {
			$this->_interface->dieMsg(
				"Die neue Kartennummer '$newCardnumber' ist gleich der " .
				'alten. Es wurde nichts verändert.'
			);
		}
		$newCardExists = $this->_em
			->getRepository('DM:BabeskCards')
			->findByCardnumber($newCardnumber);
		if($newCardExists) {
			$this->_interface->dieError(
				"Die Kartennummer '$newCardnumber' ist bereits vergeben."
			);
		}
	}
}

?>
