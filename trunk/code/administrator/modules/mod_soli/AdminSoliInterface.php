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
	
	function AskShowSoliUser($solis) {
		$this->smarty->assign('solis', $solis);
		$this->smarty->display($this->soliPath.'show_orders_select_date.tpl');
	}
	
	function ShowSoliUser($solis) {
		$this->smarty->assign('users', $solis);
		$this->smarty->display($this->soliPath.'show_soli.tpl');
	}
	
	/**
	 * Contains the Path to the mod_soli-folder
	 * @var string
	 */
	protected $soliPath;
	
}


?>