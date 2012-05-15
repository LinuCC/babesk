<?php

class AdminMealsProcessing {
	public function __construct() {
		require_once PATH_ACCESS . '/MealManager.php';
		require_once PATH_ACCESS . '/OrderManager.php';
		require_once 'AdminGroupManager.php';
		
		$this->mealManager = new MealManager();
		$this->orderManager = new OrderManager();
		
		$this->msg = array('' => '', );
	}
	
	
	
	/**
	 * Handles the MySQL-table meals
	 * @var MealManager
	 */
	protected $mealManager;
	
	/**
	 * Handles the MySQL-table orders
	 * @var OrderManager
	 */
	protected $orderManager;
	
	/**
	 * Messages shown to the user
	 * @var string[]
	 */
	protected $msg;
	
}

?>