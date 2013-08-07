<?php
/**
 *@file meals.php handles all parts of the mealsmodule and combines them with the needed sourcefiles outside (like Database-functions)
 */

require_once PATH_INCLUDE . '/Module.php';

class Meals extends Module {

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {
		// No direct access
		defined('_AEXEC') or die("Access denied");

		require_once 'AdminMealProcessing.php';
		require_once 'AdminMealInterface.php';

		$mealInterface = new AdminMealInterface($this->relPath);
		$mealProcessing = new AdminMealProcessing($mealInterface);

		$this->_acl = $dataContainer->getAcl();
		$this->_mealInterface = $mealInterface;

		if (isset($_GET["action"])) {

			switch ($_GET["action"]) {
				case 1:
					$mealProcessing->CreateMeal();
					break;
				case 2:
					$mealProcessing->ShowMeals();
					break;
				case 3:
					$mealProcessing->ShowOrders();
					break;
				case 4:
					$mealProcessing->DeleteOldMealsAndOrders();
					break;
				case 5:
					$mealProcessing->DeleteMeal($_GET['id'], true);
					break;
				case 6:
					$mealProcessing->EditInfotext();
					break;
				case 8:
					$mealProcessing->DuplicateMeal($_POST['name'], $_POST['description'], $_POST['pcID'], $_POST['date'], $_POST['max_orders']);
					break;
			}
		} else {

			//Check if new-style Submoduleexecution is used
			if($execReq = $dataContainer->getSubmoduleExecutionRequest()) {
				$this->submoduleExecute($execReq);
				die();
			}
			$mealInterface->Menu();
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Implements
	///////////////////////////////////////////////////////////////////////

	protected function submoduleMaxOrderAmountExecute() {

		if(isset($_POST['maxOrderAmount'])) {
			$this->maxOrderAmountChange();
		}
		else {
			$this->maxOrderAmountSettingDisplay();
		}
	}

	protected function maxOrderAmountChange() {

		$this->maxOrderAmountInputCheck();
		$this->maxOrderAmountUpload($_POST['maxOrderAmount']);
		$this->_mealInterface->dieSuccess(_g('The value for the maximum amount of orders per Day for a User was successfully changed'));
	}

	/**
	 * Checks the Userinput changing the Amount of Max Orders per Day and User
	 *
	 * Dies displaying an Error when Userinput not correct
	 * Uses $_POST['maxOrderAmount']
	 */
	protected function maxOrderAmountInputCheck() {

		require_once PATH_INCLUDE . '/gump.php';
		$gump = new GUMP();

		$rules = array(
			'maxOrderAmount' => array(
				'required|min_len,1|max_len,2|numeric',
				'',
				_g('Anzahl maximaler Bestellungen pro Tag')
			)
		);
		$gump->rules($rules);

		if(!$gump->run($_POST)) {
			$this->_mealInterface->dieError(
				$gump->get_readable_string_errors(true));
		}
	}

	/**
	 * Commits the Change to the Max Amount of Orders per Day to the Db
	 *
	 * Dies displaying an Errormessage on Error
	 *
	 * @param  int $amount The Amount of Orders per Day
	 */
	protected function maxOrderAmountUpload($amount) {

		try {
			if($this->maxCountOfOrdersPerDayPerUserGet() !== false) {
				TableMng::query("UPDATE global_settings SET value = '$amount'
					WHERE name = 'maxCountOfOrdersPerDayPerUser';");
			}
			else {
				TableMng::query("INSERT INTO global_settings (name, value)
					VALUES ('maxCountOfOrdersPerDayPerUser', '$amount');");
			}
		} catch (Exception $e) {
			$this->_mealInterface->dieError(_g('Could not change the Max Amount of Orders per day per User.') . $e->getMessage());
		}
	}

	/**
	 * Displays the Form to set the maxOrderAmount per Day and User
	 *
	 * Dies displaying the form or Error
	 */
	protected function maxOrderAmountSettingDisplay() {

		if(($data = $this->maxCountOfOrdersPerDayPerUserGet()) !== false) {
			$amount = (int)$data;
		}
		else {
			$amount = 0;
		}
		$this->_mealInterface->maxOrderAmountSetting($amount);
	}

	/**
	 * Fetches and returns the Allowed maximum of Orders per day and User
	 *
	 * @return string The Maximum of Orders or false if setting not found
	 */
	protected function maxCountOfOrdersPerDayPerUserGet() {

		$data = TableMng::querySingleEntry(
			'SELECT * FROM global_settings
				WHERE name = "maxCountOfOrdersPerDayPerUser"');

		if(!count($data)) {
			return false;
		}
		else {
			return $data['value'];
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	protected $_acl;

	protected $mealInterface;
}


?>
