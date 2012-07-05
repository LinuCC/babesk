<?php
require_once PATH_ADMIN.'/AdminInterface.php';
/**
 * AdminnventoryInterface is to output the Interface
 * Enter description here ...
 * @author Jan Feuchter
 *
 */
class AdminInventoryInterface extends AdminInterface{
	
	function __construct($mod_path) {
		
		parent::__construct($mod_path);
		
		$this->MOD_HEADING = $this->tplFilePath.'mod_inventory_header.tpl';
		$this->smarty->assign('inventoryParent', $this->MOD_HEADING);
	}

	function ShowSelectionFunctionality($arr_action) {
		$this->smarty->assign('action', $arr_action);
		$this->smarty->display($this->tplFilePath.'index.tpl');
	}
	
	function ShowInventory($inventory) {
		$this->smarty->assign('inventory', $inventory);
		$this->smarty->display($this->tplFilePath.'show_inventory.tpl');
	}
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>