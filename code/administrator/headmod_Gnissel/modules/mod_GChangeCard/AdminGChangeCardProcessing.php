<?php

class AdminGChangeCardProcessing {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $cardManager;
	private $userManager;
	private $cardInfoInterface;
	private $msg;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($cardInfoInterface) {

		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';

		require_once 'AdminGChangeCardInterface.php';

		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
		$this->cardInfoInterface = $cardInfoInterface;

		$this->msg = array(
			'err_card_id'			 => 'Diese Karte ist nicht vergeben!',
			'err_get_user_by_card'	 => 'Anhand der Kartennummer konnte kein Benutzer gefunden werden.',
			'err_no_orders'			 => 'Es sind keine Bestellungen für diesen Benutzer vorhanden.',
			'err_meal_not_found'	 => 'Ein Menü konnte nicht gefunden werden!',
			'err_connection'		 => 'Ein Fehler ist beim Verbinden zum MySQL-Server aufgetreten',
			'msg_order_fetched'		 => 'Die Bestellung wurde schon abgeholt',);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * Displays the names of all orders for today
	 * @param string $card_id The ID of the Card
	 */
	public function CheckCard ($card_id) {

		if (!$this->cardManager->valid_card_ID($card_id))
			$this->cardInfoInterface->dieError(sprintf($this->msg['err_card_id'], $card_id));

		$uid = $this->GetUser($card_id);
		return  $uid;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	/**
	 * Looks the user for the given CardID up, checks if the Card is locked and returns the UserID
	 * @param string $card_id The ID of the Card
	 * @return string UserID
	 */
	public function GetUser ($card_id) {
		try {
			return $this->cardManager->getUserID($card_id);
		} catch (Exception $e) {
			$this->cardInfoInterface->dieError(sprintf($this->msg['err_card_id'], $card_id));
		}

	}

	/**
	 * Returns some generic user data for identifying a card
	 */
	public function GetUserData($uid) {

		try {
			$data = TableMng::query(sprintf(
				'SELECT u.*,
				(SELECT CONCAT(g.gradelevel, g.label) AS class
					FROM usersInGradesAndSchoolyears uigs
					LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
					WHERE uigs.userId = u.ID AND
						uigs.schoolyearId = @activeSchoolyear) AS class
				FROM users u WHERE ID = %s', $uid));

		} catch (MySQLVoidDataException $e) {
			$this->cardInfoInterface->dieError('Der Benutzer wurde nicht gefunden');

		} catch (Exception $e) {
			$this->cardInfoInterface->dieError('Der Benutzer konnte nicht von der Datenbank abgerufen werden!');
		}

		return $data[0];
	}

	/**
	 * Returns the user ID
	 */
	public function GetUserID($username) {

		try {
			$data = TableMng::query(sprintf(
					'SELECT ID FROM users  WHERE username LIKE "%s"', $username));

		} catch (Exception $e) {
			$this->cardInfoInterface->dieError('Der Benutzer konnte nicht von der Datenbank abgerufen werden!');
		}

		if(!count($data)) {
			$this->cardInfoInterface->dieError("Der Benutzer '$username' wurde nicht gefunden");
		}

		return $data[0];
	}

	public function cardChange ($cardNew,$uid) {

		require_once PATH_ACCESS . '/CardManager.php';
		$cardManager = new CardManager();
		TableMng::sqlEscape($uid);

		try {
			inputcheck ($cardNew, 'card_id', 'Kartennummer');

		} catch (WrongInputException $e) {
			$this->cardInfoInterface->DieError ('Die Kartennummer enthält nicht korrekte Zeichen oder ist zu kurz.');
		}

		try {
			$card = $this->getCardDataByUserId($uid);

			if(!$card) {
				$this->cardInfoInterface->dieError(_g('The User had no cardnumber in the first place; Card not changed!'));
			}
			$cardManager->changeCardnumber($card['ID'], $cardNew);
			$cardManager->addCardIdChange($uid);

		} catch (Exception $e) {
			$this->cardInfoInterface->DieError ('Konnte die Karte nicht verändern; Ein interner Fehler ist aufgetreten');
		}

		$this->cardInfoInterface->DieSuccess(
			'Die Kartennummer wurde erfolgreich verändert');
	}

	/**
	 * Fetches the data of the Card that belongs to the User
	 *
	 * Dies displaying an Error when multiple Entries found
	 *
	 * @param  int    $userId The ID of the User
	 * @return array          The Carddata if found, else false
	 */
	public function getCardDataByUserId($userId) {

		$res = TableMng::query("SELECT * FROM BabeskCards WHERE UID = $userId");

		if(!empty($res)) {
			if(count($res) > 1) {
				$this->cardInfoInterface->dieError(_g('The User has two or more cardnumbers!'));
			}
			else {
				return $res[0];
			}
		}
		else {
			return false;
		}
	}
}

?>
