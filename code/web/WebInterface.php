<?php

require_once PATH_INCLUDE . '/GeneralInterface.php';

class WebInterface {

	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////

	public function __construct($smarty) {
		$this->_smarty = $smarty;
	}

	////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////

	/**
	 * [getSmarty description]
	 * @return [type] [description]
	 */
	public function getSmarty() {
		return $this->_smarty;
	}

	/**
	 * @param Smarty $smarty the smarty-variable to set
	 */
	public function setSmarty($smarty) {
		$this->_smarty = $smarty;
	}

	/**
	 * Sets the Backlink-Variable. It is used for showing a "Back" (German:
	 * "zuruck") - Button when the Script crashes and uses one of the
	 * die*()-Functions of this class
	 * @param string $backlink contains only the content of the href! A simple
	 * link without Html.
	 */
	public function setBacklink($backlink) {
		$this->_backlink = $backlink;
	}

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	/**
	 * Parses and displays the Content of the file $fileLink within the normal Webpage-Layout and dies
	 * @param string $fileLink The Link
	 */
	public function dieContent($fileLink) {
		$this->setSmartyBacklink();
		$content = $this->_smarty->fetch($fileLink);
		$this->_smarty->assign('content', $content);
		$this->display();
		die();
	}

	public function dieError($msg) {
		$this->setSmartyBacklink();
		$this->_smarty->append('error', $msg);
		$this->display();
		die();
	}

	public function dieMessage($msg) {
		$this->setSmartyBacklink();
		$this->_smarty->append('message', $msg);
		$this->display();
		die();
	}

	public function showError($msg) {
		$this->_smarty->append('error', $msg . '<br />');
	}

	public function showMessage($msg) {
		$this->_smarty->append('message', $msg . '<br />');
	}

	public function dieDisplay() {
		$this->setSmartyBacklink();
		$this->display();
		die();
	}

	////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////

	/**
	 * Sets the Smarty-Backlink-Variable
	 */
	protected function setSmartyBacklink() {
		if(isset($this->_backlink)) {
			$this->_smarty->assign('backlink', $this->_backlink);
		}
	}

	/**
	 * Displays the now-used Template-file with Smarty
	 * @return [type] [description]
	 */
	protected function display() {
		$this->_smarty->display('web/baseLayout.tpl');
	}

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	protected $_smarty;

	/**
	 * A link back, usable for example if the program dies with an error
	 * @var [type]
	 */
	protected $_backlink;
}

?>
