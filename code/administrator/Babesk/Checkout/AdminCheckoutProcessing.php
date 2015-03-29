<?php

class AdminCheckoutProcessing {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $cardManager;
	private $userManager;
	private $orderManager;
	private $mealManager;
	private $checkoutInterface;
	private $msg;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($checkoutInterface) {

		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/OrderManager.php';
		require_once PATH_ACCESS . '/MealManager.php';
		require_once 'AdminCheckoutInterface.php';

		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
		$this->orderManager = new OrderManager();
		$this->mealManager = new MealManager();
		$this->checkoutInterface = $checkoutInterface;

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
	public function Checkout ($card_id) {

		if (!$this->cardManager->valid_card_ID($card_id))
			$this->checkoutInterface->dieError(sprintf($this->msg['err_card_id'], $card_id));

		$uid = $this->GetUser($card_id);
		$orders = $this->GetOrders($uid);
		$mealnames = array();

		foreach ($orders as $order) {
			$mealname = $this->GetMealName($order['MID']);
			$mealnames[] = $this->OrderFetched($order['ID'], $mealname);
		}

		$this->checkoutInterface->Checkout($mealnames);
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
				$this->checkoutInterface->CardLocked();
			}
		} catch (Exception $e) {
			$this->checkoutInterface->dieError(_g('Could not find the User by Cardnumber %1$s', $card_id));
		}
		return $uid;
	}

	/**
	 * Gets all orders of today for the User with the ID $uid
	 * @param string $uid The UserID
	 * @return array() The Orders
	 */
	public function GetOrders ($uid) {

		$date = date("Y-m-d");
		try {
			$orders = $this->orderManager->getAllOrdersOfUserAtDate($uid, $date);
		} catch (MySQLVoidDataException $e) {
			$this->checkoutInterface->dieError($this->msg['err_no_orders']);
		}
		catch (Exception $e) {
			$this->checkoutInterface->dieError($e->getMessage);
		}
		return $orders;
	}

	/**
	 * Fetches the Mealname for the given MealID $mid from the MySQL-Server
	 * @param string $mid The ID of the meal
	 * @return string The mealname
	 */
	public function GetMealName ($mid) {

		try {
			$mealname = $this->mealManager->GetMealName($mid);
		} catch (MySQLVoidDataException $e) {
			/**
			 * @FIXME Error should not kill whole Process, just one Menu couldnt be fetched!
			 */
			$this->checkoutInterface->dieError($this->msg['err_meal_not_found']);
		}
		catch (Exception $e) {
			$this->checkoutInterface->dieError($this->msg['err_connection'] . '<br>' . $e->getMessage());
		}
		return $mealname;
	}

	/**
	 * Looks up if the Order is already fetched. If no,it sets it to fetched.
	 * If yes, it changes the Mealname to let the User know that it is already fetched.
	 * @param string $order_id The ID of the Order
	 * @param string $mealname The Mealname
	 * @return string The Final Mealname
	 */
	public function OrderFetched ($order_id, $mealname) {

		$final_mealname;
		if (!$this->orderManager->OrderFetched($order_id)) {
			$final_mealname = $mealname;
			$this->orderManager->setOrderFetched($order_id);
		} else {
			$final_mealname = $this->msg['msg_order_fetched'] . ' : ' . $mealname;
		}
		return $final_mealname;
	}
}

?>
