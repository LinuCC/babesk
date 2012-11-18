<?php
/**
 *@file meals.php handles all parts of the mealsmodule and combines them with the needed sourcefiles outside (like Database-functions)
 */

require_once PATH_INCLUDE . '/Module.php';

class Meals extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		// No direct access
		defined('_AEXEC') or die("Access denied");

		require_once 'AdminMealProcessing.php';
		require_once 'AdminMealInterface.php';

		$mealInterface = new AdminMealInterface($this->relPath);
		$mealProcessing = new AdminMealProcessing($mealInterface);

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
				case 7:
					$mealProcessing->EditLastOrderTime();
					break;
				case 8:
					$mealProcessing->DuplicateMeal($_POST['name'], $_POST['description'], $_POST['pcID'], $_POST['date'], $_POST['max_orders']);
					break;
			}
		} else {

			$mealInterface->Menu();
		}
	}
}


?>