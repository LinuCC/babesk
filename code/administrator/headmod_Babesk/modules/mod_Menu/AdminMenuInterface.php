<?php

require_once PATH_ADMIN . '/AdminInterface.php';

class AdminMenuInterface extends AdminInterface {
	public function __construct($from_module, $mod_path) {
		
		parent::__construct($mod_path);
		
		$this->isFromModule = $from_module;
	}
	
	public function AdditionalHeader() {
		$this->smarty->display(PATH_SMARTY . '/templates/administrator/modules/mod_menu/menu_header.tpl');
	}
	
	public function ShowError($msg){
		if($this->isFromModule)
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
			$this->smarty->assign('menu_table', $this->smarty->fetch($this->tplFilePath . 'menu_table.tpl'));
			$this->smarty->display($this->tplFilePath . 'formatted_menu_table.tpl');
		} else {
			$this->smarty->display($this->tplFilePath . 'menu_table.tpl');
		}
	}
	
	protected $isFromModule;
}

?>