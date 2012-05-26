<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class AdminMenuInterface extends AdminInterface {
	public function __construct($from_module) {
		
		parent::__construct();
		
		$this->isFromModule = $from_module;
		$this->folderPath = PATH_SMARTY_ADMIN_MOD . '/mod_menu/';
	}
	
	public function AdditionalHeader() {
		$this->smarty->display(PATH_SMARTY . '/templates/administrator/modules/mod_menu/menu_header.tpl');
	}
	
	public function ShowError($msg){
		if($isFromModule)
			parent::ShowError($msg);
		else
			die('Fehler:' . $msg);
	}
	
	public function ShowMsg($msg){
		if($isFromModule)
			parent::ShowMsg($msg);
		else
			die($msg);
	}
	
	public function Menu($infotext1, $infotext2, $meallistweeksorted, $weekdate) {
		
		$this->smarty->assign('menu_text1', $infotext1);
		$this->smarty->assign('menu_text2', $infotext2);
		$this->smarty->assign('meallistweeksorted', $meallistweeksorted);
		$this->smarty->assign('weekdate', $weekdate);
		
		if ($this->isFromModule) {
			$this->smarty->assign('menu_table', $this->smarty->fetch($this->folderPath . 'menu_table.tpl'));
			$this->smarty->display($this->folderPath . 'formatted_menu_table.tpl');
		} else {
			$this->smarty->display($this->folderPath . 'menu_table.tpl');
		}
	}
	
	protected $folderPath;
	protected $isFromModule;
}

?>