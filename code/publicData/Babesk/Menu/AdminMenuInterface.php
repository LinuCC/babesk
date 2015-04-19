<?php

require_once PATH_PUBLICDATA . '/PublicDataInterface.php';

class AdminMenuInterface extends PublicDataInterface {
	public function __construct($smarty, $path) {
		parent::__construct($smarty, $path);
	}

	public function AdditionalHeader() {
		$this->_smarty->display(PATH_SMARTY_TPL . '/publicData/Babesk/Menu/menu_header.tpl');
	}

	public function Menu($infotext1, $infotext2, $meallistweeksorted, $weekdate) {

		$this->_smarty->assign('menu_text1', $infotext1);
		$this->_smarty->assign('menu_text2', $infotext2);
		$this->_smarty->assign('meallistweeksorted', $meallistweeksorted);
		$this->_smarty->assign('weekdate', $weekdate);

		$this->_smarty->display($this->_smartyModTemplates . 'menu_table.tpl');
	}
}

?>