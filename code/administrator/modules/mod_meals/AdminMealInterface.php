<?php

class AdminMealInterface extends AdminInterface {
	public function __construct() {
		parent::__construct();
		$this->folderPath = PATH_SMARTY_ADMIN_MOD . '/mod_meals/';
		$this->parentPath = $this->folderPath . 'meals_header.tpl';
		$this->smarty->assign('mealParent', $this->parentPath);
	}
	
	public function Menu() {
		$this->smarty->display($this->folderPath . 'meals_initial_menu.tpl');
	}
	
	public function AddMeal($pc_ids, $pc_names) {
		$this->smarty->assign('price_class_id', $pc_ids);
		$this->smarty->assign('price_class_name', $pc_names);
		$this->smarty->display($this->folderPath . 'add_meal.tpl');
	}
	
	public function ShowMeals($meals) {
		$this->smarty->assign('meals', $meals);
		$this->smarty->display($this->folderPath . 'show_meals.tpl');
	}

	public function ShowOrders ($num_orders, $orders, $ordering_date) {
		$this->smarty->assign('num_orders', $num_orders);
		$this->smarty->assign('orders', $orders);
		$this->smarty->assign('ordering_date', $ordering_date);
		$this->smarty->display($this->folderPath . 'show_orders.tpl');
	}
	
	/**
	 * Shows a form to the user to select the date of orders to be displayed
	 * @param array(day,month,year) $today
	 */
	public  function ShowOrdersSelectDate ($today) {
		$this->smarty->assign('today', $today);
		$this->smarty->display($this->folderPath . 'show_orders_select_date.tpl');
	}
	
	public function EditInfotexts($it1, $it2) {
		$this->smarty->assign('infotext1', $it1);
		$this->smarty->assign('infotext2', $it2);
		$this->smarty->display($this->folderPath. 'edit_infotext.tpl');
	}
	
	public function FinEditInfotexts ($it1, $it2) {
		$this->smarty->assign('infotext1', $it1);
		$this->smarty->assign('infotext2', $it2);
		$this->smarty->display($this->folderPath . 'edit_infotext_fin.tpl');
	}

	public function EditLastOrderTime ($time) {
		$this->smarty->assign('lastOrderTime', $time);
		$this->smarty->display($this->folderPath . 'edit_deadline.tpl');
	}
	
	public  function  DuplicateMeal($pc_ids, $pc_names, $name, $description, $pc_ID, $max_orders, $date) {
		
		$this->smarty->assign('price_class_id', $pc_ids);
		$this->smarty->assign('price_class_name', $pc_names);
		
		$this->smarty->assign('name_str', $name);
		$this->smarty->assign('descr_str', $description);
		$this->smarty->assign('pc_str', $pc_ID);
		$this->smarty->assign('max_order_str', $max_orders);
		$this->smarty->assign('date_str', $date);
		
		$this->smarty->display($this->folderPath . 'add_meal.tpl');
		
	}
	
	
	private $folderPath;
}

?>