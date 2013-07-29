<?php

require_once PATH_INCLUDE . '/Module.php';

class InputdataCheck extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {


		if(isset($_GET['gump'])) { //Use other method of checking
			$this->gump();
		}
		else {
			$this->variablesFetch();

			try {
				inputcheck($this->_userInput, $this->_regex, $this->_elementName);
			} catch (WrongInputException $e) {
				die('wrongInput');
			} catch (Exception $e) {
				die('somethingWentWrong' . $e->getMessage());
			}
			die('correctInput');
		}

	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function variablesFetch() {

		if(isset($_POST['userInput'], $_POST['regex'], $_POST['elementName'])) {
			$this->_userInput = $_POST['userInput'];
			$this->_regex = $_POST['regex'];
			$this->_elementName = $_POST['elementName'];
		}
		else {
			die('parametersMissing');
		}
	}

	protected function gump() {

		require_once PATH_INCLUDE . '/gump.php';

		try {
			$gump = new GUMP($_POST);

			$gump->rules(array('userInput' => array($_POST['regex'], '',
							$_POST['elementName'])));

			if(!$gump->run($_POST)) {
				die('wrongInput');
			}
			else {
				die('correctInput');
			}

		} catch (Exception $e) {
			die('somethingWentWrong' . $e->getMessage());
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_interface;

	protected $_userInput;

	protected $_regex;

	protected $_elementName;
}

?>
