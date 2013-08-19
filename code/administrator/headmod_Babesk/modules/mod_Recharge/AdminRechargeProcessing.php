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
			// $max_amount = $this->userManager->getMaxRechargeAmount($uid);
			$max_amount = $this->getMaxRechargeAmountOfUser($uid);
			$max_amount = sprintf('%01.2f', $max_amount);
		} catch (Exception $e) {
			$this->rechargeInterface->dieError($this->msg['err_fetch_max_credit']);
		}

		$this->rechargeInterface->ChangeAmount($max_amount, $uid);
	}

	/**
	 * Fetches the Max allowed Amount that the user can recharge
	 *
	 * @param  int    $userId The ID of the User
	 * @return int            The Maximum allowed amount to recharge
	 */
	protected function getMaxRechargeAmountOfUser($userId) {

		try {
			$data = TableMng::querySingleEntry(
				"SELECT g.max_credit AS maxCredits, u.credit AS credits
				FROM users u
				JOIN groups g ON u.GID = g.ID
				WHERE u.ID = $userId");

		} catch (Exception $e) {
			$this->rechargeInterface->dieError(_g('Could not fetch the Max ' .
				'Credits for the User with the ID %1$s', $userId));
		}

		if(!isset($data) || !count($data)) {
			$this->rechargeInterface->dieError(_g('Could not fetch the Max ' .
				'Credits for the User with the ID %1$s; It looks like ' .
				'the User is not in any Pricegroup?', $userId));
		}

		return $data['maxCredits'] - $data['credits'];
	}

	/**
	 * Recharges the card
	 * @param string $uid The User-ID
	 * @param string $recharge_amount The Amount of Money to recharge
	 */
	// public function RechargeCard($uid, $recharge_amount) {

	// 	$recharge_amount = str_replace(',', '.', $recharge_amount);
	// 	$recharge_amount = floatval($recharge_amount);
	// 	try {
	// 		$this->userManager->changeBalance($uid, $recharge_amount);
	// 	} catch (Exception $e) {
	// 		$this->rechargeInterface->dieError(
	// 			$this->msg['err_change_balance']);
	// 	}
	// 	try {
	// 		$username = $this->userManager->getUsername($uid);
	// 	} catch (Exception $e) {
	// 		$username = 'ERROR';
	// 	}

	// 	$this->rechargeInterface->RechargeCard($username, $recharge_amount);
	// }

	/**
	 * Recharges the Card of the User by the $rechargeAmount
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  int    $userId         The ID of the User which Card to recharge
	 * @param  float  $rechargeAmount The Amount to recharge [add]
	 */
	public function rechargeCard($userId, $rechargeAmount) {

		$rechargeAmount = floatval(str_replace(',', '.', $rechargeAmount));

		if($rechargeAmount <= $this->getMaxRechargeAmountOfUser($userId)) {
			$this->addAmountToUserCredits($rechargeAmount, $userId);
		}
		else {
			$this->rechargeInterface->dieError(_g('The given amount to recharge added to the Credits is more than the Maximum Amount of Credits allowed for the Users Pricegroup!'));
		}

		$this->rechargeSuccessDisplay($rechargeAmount, $userId);
	}

	/**
	 * Adds the given Amount to the Users Credits
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param float  $amount The Amount of Credits to add
	 * @param int    $userId The ID of the User which credits to change
	 */
	public function addAmountToUserCredits($amount, $userId) {

		try {
			TableMng::query("UPDATE users SET credit = credit + '$amount'
				WHERE ID = $userId");

		} catch (Exception $e) {
			$this->rechargeInterface->dieError(_g('Could not upload the recharge of the credits!'));
		}
	}

	/**
	 * Displays a Success-Message to the User that the Recharge was successfull
	 *
	 * Dies displaying a Message
	 *
	 * @param  float  $amount The Amount that was reloaded
	 * @param  int    $userId The ID of the User
	 */
	public function rechargeSuccessDisplay($amount, $userId) {

		$data = TableMng::querySingleEntry("SELECT username FROM users
			WHERE ID = $userId");

		if(isset($data) && count($data)) {
			$username = $data['username'];
		}
		else {
			$username = 'Username not found!';
		}

		$this->rechargeInterface->RechargeCard($username, $amount);
	}


	protected $cardManager;
	protected $userManager;
	protected $rechargeInterface;
	protected $msg;

}

?>
