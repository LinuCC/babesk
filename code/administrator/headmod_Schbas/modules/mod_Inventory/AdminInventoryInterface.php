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
	
	function ShowInventory($bookcodes,$navbar) {
		$this->smarty->assign('bookcodes', $bookcodes);
		$this->smarty->assign('navbar', $navbar);
		$this->smarty->display($this->tplFilePath.'show_inventory.tpl');
	}
	
	function ShowChangeInv($bookdata, $invdata) {
		$this->smarty->assign('bookdata', $bookdata);
		$this->smarty->assign('invdata', $invdata);
		$this->smarty->display($this->tplFilePath.'change_inv.tpl');
	}
	
	function ShowChangeInvFin($id, $purchase, $exemplar) {
		$this->smarty->assign('id', $id);
		$this->smarty->assign('purchase', $purchase);
		$this->smarty->assign('exemplar', $exemplar);
		$this->smarty->display($this->tplFilePath.'change_inv_fin.tpl');
	}
	
	function ShowDeleteConfirmation($id) {
		$this->smarty->assign('id', $id);
		$this->smarty->display($this->tplFilePath.'deletion_confirm.tpl');
	}
	
	function ShowDeleteFin() {
		$this->smarty->display($this->tplFilePath.'deletion_finished.tpl');
	}
	
	function ShowAddEntry() {
		$this->smarty->display($this->tplFilePath.'add_entry.tpl');
	}
	
	function ShowAddEntryFin($book_info, $year_of_purchase,$exemplar) {
		$this->smarty->assign('book_info', $book_info);
		$this->smarty->assign('purchase', $year_of_purchase);
		$this->smarty->assign('exemplar', $exemplar);
		$this->smarty->display($this->tplFilePath.'add_entry_fin.tpl');
	}
	
	/**
	 * The Path to the Smarty-Parent-Templatefile
	 */
	protected $MOD_HEADING;
}
?>