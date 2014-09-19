<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_System/System.php';

class CardInfo extends System {

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {
		//no direct access
		defined('_AEXEC') or die("Access denied");
		parent::entryPoint($dataContainer);
		parent::initSmartyVariables();

		require_once 'AdminCardInfoProcessing.php';
		require_once 'AdminCardInfoInterface.php';

		$cardInfoInterface = new AdminCardInfoInterface($this->relPath);
		$cardInfoProcessing = new AdminCardInfoProcessing($cardInfoInterface);

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['card_ID'])) {
			$this->cardinfoDisplay($_POST['card_ID']);
			$uid = $cardInfoProcessing->CheckCard($_POST['card_ID']);
			$userData = $this->getUserData($uid);
			$this->_smarty->assign('cardID', $_POST['card_ID']);
			$this->_smarty->assign('name', $userData['name']);
			$this->_smarty->assign('forename', $userData['forename']);
			$this->_smarty->assign('class', $userData['class']);
			$this->_smarty->assign('locked', $userData['locked']);
			$this->displayTpl('result.tpl');
		}
		else{
			$this->displayTpl('form.tpl');
		}
	}

	////////////////////////////////////////////////////////////////////////
	//Implements
	////////////////////////////////////////////////////////////////////////

	protected function getUserData($userId) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT u.*, CONCAT(g.gradelevel, g.label) AS class
					FROM SystemUsers u
					LEFT JOIN SystemUsersInGradesAndSchoolyears uigs
						ON uigs.userId = u.ID
						AND uigs.schoolyearId = @activeSchoolyear
					LEFT JOIN SystemGrades g ON g.ID = uigs.gradeId
					WHERE u.ID = :userId
			');
			$stmt->execute(array('userId' => $userId));
			return $stmt->fetch();

		} catch (PDOException $e) {
			$this->_logger->log('Error fetching the user',
				'Notice', Null, json_encode(array('uid' => $userId)));
			$this->_interface->dieError(
				'Ein Fehler ist beim Abrufen des Benutzers aufgetreten!'
			);
		}
	}

	private function cardinfoDisplay($cardnumber) {

		$card = $this->_entityManager->getRepository('Babesk:BabeskCards')
			->findOneByCardnumber($cardnumber);
		if($card) {
			try {
				$user = $card->getUser();
				$user->getForename(); //Force loading of user-proxy to Entity

			} catch (Doctrine\ORM\EntityNotFoundException $e) {
				$this->_logger->log('Card exists, but linked user not!',
					'Moderate', Null, json_encode(array(
						'cardnumber' => $cardnumber)));
				$this->_interface->dieError(
					'Karte an einen nicht-existenten Benutzer vergeben!'
				);
			}
			$grade = $this->_entityManager
				->getRepository('Babesk:SystemUsers')
				->getActiveGradeByUser($user);
			$this->cardinfoRender($card, $user, $grade);
		}
		else {
			$this->_interface->dieError('Karte ist nicht vergeben.');
		}
		die();
	}

	private function cardinfoRender($card, $user, $grade) {

		$this->_smarty->assign('card', $card);
		$this->_smarty->assign('user', $user);
		$this->_smarty->assign('grade', $grade);
		$this->displayTpl('result.tpl');
	}
}

?>
