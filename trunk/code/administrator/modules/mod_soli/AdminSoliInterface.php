<?php 


class AdminSoliInterface extends AdminInterface {
	function __construct(){
		parent::__construct();
		$this->soliPath = PATH_SMARTY_ADMIN_MOD.'/mod_soli/';
		$this->parentPath = PATH_SMARTY_ADMIN_MOD.'/mod_soli/header_soli.tpl';
		$this->smarty->assign('soliParent', $this->parentPath);
	}
	
	function ShowInitialMenu() {
		$this->smarty->display($this->soliPath.'soli_initial_menu.tpl');
	}
	
	function AddCoupon($solis) {
		$this->smarty->assign('solis', $solis);
		$this->smarty->display($this->soliPath.'add_coupon.tpl');
	}
	
	function ShowCoupon($coupons) {
		$this->smarty->assign('coupons', $coupons);
		$this->smarty->display($this->soliPath.'show_coupons.tpl');
	}
	
	function ConfirmDelCoupon($id, $username) {
		$this->smarty->assign('id', $id);
		$this->smarty->assign('username', $username);
		$this->smarty->display($this->soliPath.'conf_del_coupon.tpl');
	}
	
	/**
	 * This function shows specific orders of soli-Users
	 * 
	 * @param Array $orders The orders-array. it has 2 dimensions; the first dimension is every single order, the second
	 * the specific order-data (@see show_orders.tpl)
	 * @param integer $weeknum the number of the week
	 * @param string $username The Username whose orders should be displayed
	 * @param float $sum_pricediff The overall Pricedifference of all orders (difference between soli_price and standard-
	 * mealprice
	 */
	function ShowSpecOrders($orders, $weeknum, $username, $sum_pricediff) {
		$this->smarty->assign('orders', $orders);
		$this->smarty->assign('ordering_date', $weeknum);
		$this->smarty->assign('name', $username);
		$this->smarty->assign('sum', $sum_pricediff);
		$this->smarty->display($this->soliPath.'show_orders.tpl');
	}
	
	function AskShowSoliUser($solis) {
		$this->smarty->assign('solis', $solis);
		$this->smarty->display($this->soliPath.'show_orders_select_date.tpl');
	}
	
	function ShowSoliUser($solis) {
		$this->smarty->assign('users', $solis);
		$this->smarty->display($this->soliPath.'show_soli.tpl');
	}
	
	function ChangeSettings($soli_price) {
		$this->smarty->assign('old_price', $soli_price);
		$this->smarty->display($this->soliPath.'show_settings.tpl');
	}
	
	/**
	 * Contains the Path to the mod_soli-folder
	 * @var string
	 */
	protected $soliPath;
	
}


?>