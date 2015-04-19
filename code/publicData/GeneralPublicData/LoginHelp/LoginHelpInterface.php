<?php

class LoginHelpInterface extends PublicDataInterface {
	public function __construct($smarty, $path) {
		parent::__construct($smarty, $path);
	}

	public function helptextShow ($txt) {
		$this->_smarty->assign ('helptext', $txt);
		$this->_smarty->display ($this->_smartyModTemplates . 'showHelptext.tpl');
	}
}

?>