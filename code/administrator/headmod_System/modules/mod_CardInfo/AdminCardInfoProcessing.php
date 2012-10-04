<?php

class AdminCardInfoProcessing {

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
		
		require_once 'AdminCardInfoInterface.php';

		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
		$this->cardInfoInterface = $cardInfoInterface;

		$this->msg = array(
			'err_card_id'			 => 'Dies ist keine gültige Karten-ID ("%s")',
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
			$this->fitsCheckInterface->dieError(sprintf($this->msg['err_card_id'], $card_id));

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
			$uid = $this->cardManager->getUserID($card_id);
			if ($this->userManager->checkAccount($uid)) {
				$this->fitsCheckInterface->CardLocked();
			}
		} catch (Exception $e) {
			$this->fitsCheckInterface->dieError($this->msg['err_get_user_by_card'] . ' Error:' . $e->getMessage());
		}
		return $uid;
	}
	
	/**
	 * Returns some generic user data for identifying a card
	 */
	public function GetUserData($uid) {
		return $this->userManager->getUserdata($uid);
	}
}

?>