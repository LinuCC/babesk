<?php

require_once dirname(__FILE__) . '/KuwasysDataContainer.php';
require_once PATH_INCLUDE . '/HeadModule.php';

/**
 * class for Interface administrator
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class Kuwasys extends HeadModule {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name) {
		
		parent::__construct($name, $display_name);
		
		$this->_dataContainer = new KuwasysDataContainer();
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute() {
		//function not needed, javascript is doing everything
	}
}
?>