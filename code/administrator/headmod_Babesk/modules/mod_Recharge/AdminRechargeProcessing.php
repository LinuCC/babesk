<?php

class AdminRechargeProcessing {
	public function __construct($rechargeInterface) {

		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';

		$this->rechargeInterface = $rechargeInterface;
		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();

		$this->msg = array(
				'err_card_locked' => 'Diese Karte ist gesperrt und kann daher nicht aufgeladen werden.',
				'err_user_not_found' => 'Es konnte kein zur Karte hinzugehöriger Benutzer gefunden werden.',
				'err_fetch_max_credit' => 'Ein Fehler ist beim Abrufen des Maximalen Guthabens entstanden.',
				'err_change_balance' => 'Ein Fehler ist beim Verarbeiten der Daten aufgetreten.',
		);
	}

	/**
	 * Displays the Form to change the Amount of money to reload
	 * @param string $card_id The ID of the Card
	 */
	public function ChangeAmount($card_id) {

		try {
			$uid = $this->cardManager->getUserID($card_id);
			if($this->userManager->checkAccount($uid))
				$this->rechargeInterface->dieError($this->msg['err_card_locked']);
		} catch (Exception $e) {
			$this->rechargeInterface->dieError($this->msg['err_user_not_found']);
		}

		try {
			$max_amount = $this->userManager->getMaxRechargeAmount($uid);
			$max_amount = sprintf('%01.2f', $max_amount);
		} catch (Exception $e) {
			$this->rechargeInterface->dieError($this->msg['err_fetch_max_credit']);
		}

		$this->rechargeInterface->ChangeAmount($max_amount, $uid);
	}

	/**
	 * Recharges the card
	 * @param string $uid The User-ID
	 * @param string $recharge_amount The Amount of Money to recharge
	 */
	public function RechargeCard($uid, $recharge_amount) {

		$recharge_amount = str_replace(',', '.', $recharge_amount);
		$recharge_amount = floatval($recharge_amount);
		try {
			$this->userManager->changeBalance($uid, $recharge_amount);
		} catch (Exception $e) {
			$this->rechargeInterface->dieError(
				$this->msg['err_change_balance']);
		}
		try {
			$username = $this->userManager->getUsername($uid);
		} catch (Exception $e) {
			$username = 'ERROR';
		}

		$this->rechargeInterface->RechargeCard($username, $recharge_amount);
	}


	protected $cardManager;
	protected $userManager;
	protected $rechargeInterface;
	protected $msg;

}

?>
