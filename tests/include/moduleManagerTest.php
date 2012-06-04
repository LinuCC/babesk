<?php

require_once '../path.php';
require_once PATH_CODE . 'include/moduleManager.php';

class ModuleManagerTest extends PHPUnit_Framework_TestCase {
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	private $_moduleManager;
	
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	protected function setUp() {
		
		$this->_moduleManager = new ModuleManager();
	}
	
	/**
	 * @dataProvider providerParseModuleXML
	 */
	public function testParseModuleXML($programPart, $expectedOutcome) {
		
		$this->assertEqual
	}
	
	public function providerParseModuleXML() {
		
		return array(
				array('administrator', true),
				array('web', true),
				array('veeeeryWrongInterFaCE', false),
				array(1256, false),
				);
	}
}

?>