<?php

require_once PATH_INCLUDE . '/Module.php';

/**
 * Displays a help-text to the user
 */
class Help extends Module {

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name,$headmod_menu) {

		parent::__construct($name, $display_name,$headmod_menu);
	}

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$text = $this->helptextFetch();
		if(!$text) {
			$this->_interface->dieError(_g('The helptext is void'));
		}
		$this->_smarty->assign('helptext', $text);
		$this->displayTpl('main.tpl');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->moduleTemplatePathSet();
	}

	/**
	 * Fetches the helptext from the database
	 * @return string The helptext as a string or false if entry not found
	 */
	protected function helptextFetch() {

		try {
			$res = $this->_pdo->query(
				'SELECT value FROM SystemGlobalSettings
					WHERE name="helptext"');

			return $res->fetchColumn();

		} catch (\PDOException $e) {
			$this->_logger->log('error fetching the helptext',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not fetch the helptext.'));
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////
}

?>
