<?php

require_once PATH_INCLUDE . '/GeneralInterface.php';

class WebInterface {

	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////

	public function __construct($smarty) {
		$this->_smarty = $smarty;
		$this->_baseTemplate = PATH_SMARTY_TPL . '/web/baseLayout.tpl';
		$this->_smarty->assign(
			'inh_path', $this->_baseTemplate
		);
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
	 * Adds a button to the bottom of the main content that links to somewhere
	 * @param string $name The text of the button
	 * @param string $link Where the button should link to (like
	 * 'index.php?YourParamsHere')
	 */
	public function addButton($name, $link) {
		$this->_buttonlinks[] = array('name' => $name, 'link' => $link);
		$this->_smarty->assign('buttonlinks', $this->_buttonlinks);
	}

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
		$this->_smarty->display($this->_baseTemplate);
	}

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	protected $_smarty;

	/**
	 * A link back, usable for example if the program dies with an error
	 * @var string
	 */
	protected $_backlink;

	protected $_buttonlinks;

	/**
	 * The base-smarty-template that gets inherited to display the side
	 * @var string
	 */
	protected $_baseTemplate;
}

?>
