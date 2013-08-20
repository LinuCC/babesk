<?php

require_once PATH_INCLUDE . '/Module.php';

class Recharge extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Module
	 *
	 * @param string $name         The Name of the Module
	 * @param string $display_name The Name that should be displayed to the
	 *                             User
	 * @param string $path         A relative Path to the Module
	 */
	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the Module, does things based on ExecutionRequest
	 *
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		if($execReq = $dataContainer->getSubmoduleExecutionRequest()) {
			$this->submoduleExecute($execReq);
		}
		else {
			$this->displayTpl('mainmenu.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * The Entry-Point of this Module, initializes the needed Data
	 *
	 * Dies displaying a Message when User tries to access from outside
	 *
	 * @param DataContainer $dataContainer contains data needed by the Module
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::initSmartyVariables();
		$this->_interface = $dataContainer->getInterface();
	}

	/********************************************************************
	 * Allows the User to recharge the Credits of a User by his Card
	 */
	protected function submoduleRechargeCardExecute() {

		if(isset($_POST['card_ID'])) {
			$this->changeAmountDisplay($_POST['card_ID']);
		}
		else if(isset($_POST['amount'], $_POST['uid'])) {
			$this->rechargeUsercredits($_POST['amount'], $_POST['uid']);
		}
		else {
			$this->displayTpl('form1.tpl');
		}
	}

	/**
	 * Displays a form allowing the User to Change the Recharge-Amount
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  int    $cardId The ID of the Card
	 */
	protected function changeAmountDisplay($cardId) {

		$userId = $this->userIdGetByCardId($cardId);

		if($this->isUseraccountUnlockedCheck($userId)) {

			$maxRechargeAmount = $this->getMaxRechargeAmountOfUser($userId);
			$maxRechargeAmount = sprintf('%01.2f', $maxRechargeAmount);

			$this->_smarty->assign('max_amount', $maxRechargeAmount);
			$this->_smarty->assign('uid', $userId);
			$this->displayTpl('form2.tpl');
		}
		else {
			$this->_interface->dieError(_g('Useraccount is locked!'));
		}
	}

	/**
	 * Fetches the User-ID by the given Card-ID
	 *
	 * Dies displaying a Message if Error connecting to the DB happened
	 *
	 * @param int    $cardId The Card-ID of the User-Id to get
	 * @return string The User-ID if found else false
	 */
	protected function userIdGetByCardId($cardId) {

		try {
			$stmt = $this->_pdo->prepare('SELECT UID FROM cards
				WHERE cardnumber = :cardnumber');

			$stmt->execute(array('cardnumber' => $cardId));

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not fetch the User by Card-ID %1$s', $cardId));
		}

		return $stmt->fetchColumn();
	}

	/**
	 * Checks if the Useraccount is locked or not
	 *
	 * Dies displaying a Message if connecting to Database failed
	 *
	 * @param  int     $userId The ID of the Useraccount to check
	 * @return boolean         true if it is not locked or false if Account
	 * locked
	 */
	protected function isUseraccountUnlockedCheck($userId) {

		try {
			$stmt = $this->_pdo->prepare('SELECT locked FROM users
				WHERE ID = :userId');

			$stmt->execute(array('userId' => $userId));

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not check if the ' .
				'Useraccount of User-ID %1$s is locked or not', $userId));
		}

		if(($data = $stmt->fetchColumn()) === false) {
			$this->_interface->dieError(_g('Could not check if the ' .
				'Useraccount of User-ID %1$s is locked or not. Could ' .
				'not find the User!', $userId));
		}
		else {
			return $data == '0';
		}
	}

	/**
	 * Fetches the Max allowed Amount that the user can recharge
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  int    $userId The ID of the User
	 * @return int            The Maximum allowed amount to recharge
	 */
	protected function getMaxRechargeAmountOfUser($userId) {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT g.max_credit AS maxCredits, u.credit AS credits
				FROM users u
				JOIN groups g ON u.GID = g.ID
				WHERE u.ID = :userId');

			$stmt->execute(array('userId' => $userId));

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not fetch the Max ' .
				'Credits for the User with the ID %1$s', $userId));
		}

		if($data = $stmt->fetch()) {
			return $data['maxCredits'] - $data['credits'];
		}
		else {
			$this->_interface->dieError(_g('Could not fetch the ' .
				'Max Credits for the User with the ID %1$s; It looks ' .
				'like the User is not in any Pricegroup?', $userId));
		}
	}

	/**
	 * Recharges the Card of the User by the $rechargeAmount
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  int    $userId         The ID of the User which Card to recharge
	 * @param  float  $rechargeAmount The Amount to recharge [add]
	 */
	protected function rechargeUsercredits($rechargeAmount, $userId) {

		$rechargeAmount = floatval(str_replace(',', '.', $rechargeAmount));

		if($rechargeAmount <= $this->getMaxRechargeAmountOfUser($userId)) {

			$this->_pdo->beginTransaction();

			$this->amountAddToUsercredits($rechargeAmount, $userId);
			$this->trackRechargeAdd($rechargeAmount, $userId);

			$this->_pdo->commit();
		}
		else {
			$this->_interface->dieError(_g('The given amount to recharge added to the Credits is more than the Maximum Amount of Credits allowed for the Users Pricegroup!'));
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
	protected function amountAddToUsercredits($amount, $userId) {

		try {
			$stmt = $this->_pdo->prepare('UPDATE users
				SET credit = credit + :amount WHERE ID = :userId');

			$stmt->execute(array('amount' => $amount, 'userId' => $userId));

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not upload the recharge ' .
				'of the credits!') . $e->getMessage());
		}
	}

	/**
	 * Adds an Row to the Table that tracks the Recharges
	 *
	 * Dies displaying a Message on Error
	 *
	 * @param  float  $amount The Amount of Credits to recharge
	 * @param  int    $userId The User-ID of the Card to recharge
	 */
	protected function trackRechargeAdd($amount, $userId) {

		try {
			$stmt = $this->_pdo->prepare('INSERT INTO usercreditsRecharges
				(userId, rechargingUserId, rechargeAmount, datetime) VALUES
				(:userId, :rechargingUserId, :rechargeAmount, :datetime)');

			$stmt->execute(array(
				'userId' => $userId,
				'rechargingUserId' => $_SESSION['UID'],
				'rechargeAmount' => $amount,
				'datetime' => date( 'Y-m-d H:i:s')
			));

		} catch (PDOException $e) {

			$this->_interface->dieError(_g('Could not track the Recharge!'));
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
	protected function rechargeSuccessDisplay($amount, $userId) {

		$stmt = $this->_pdo->prepare(
			'SELECT CONCAT(forename, " ", name) FROM users
			WHERE ID = :userId');
		$stmt->execute(array('userId' => $userId));

		if(!($username = $stmt->fetchColumn())) {
			$username = _g('Username not found!');
		}

		$this->_smarty->assign('username', $username);
		$this->_smarty->assign('amount', sprintf('%01.2f', $amount));
		$this->displayTpl('recharge_success.tpl');
	}

	/********************************************************************
	 * Allows the User to Print a Balance of the Recharges done
	 */
	protected function submodulePrintRechargeBalanceExecute() {

		$this->displayTpl('printRechargeBalanceSelect.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_interface;
}

?>
