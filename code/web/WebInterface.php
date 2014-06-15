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
		$this->_isAjax = false;
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

	/**
	 * If ajax-responses are enabled or not
	 * @return bool true if it is enabled, false if not
	 */
	public function getAjax() {

		return $this->_isAjax;
	}

	/**
	 * Enables / disables ajax
	 * @param bool $isAjax true if ajax-responses should be enabled,
	 *                     false if not
	 */
	public function setAjax($isAjax) {

		$this->_isAjax = $isAjax;
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
	public function addButton($name, $link, $type = 'default') {
		$this->_buttonlinks[] = array(
			'name' => $name,
			'link' => $link,
			'type' => $type
		);
		$this->_smarty->assign('buttonlinks', $this->_buttonlinks);
	}

	/**
	 * Parses and displays the Content of the file $fileLink within the normal Webpage-Layout and dies
	 * @param string $fileLink The Link
	 */
	public function dieContent($fileLink) {
		if(!$this->_isAjax) {
			$this->setSmartyBacklink();
			$content = $this->_smarty->fetch($fileLink);
			$this->_smarty->assign('content', $content);
			$this->display();
			die();
		}
		else {
			die('Not supported with ajax!');
		}
	}

	public function dieError($msg) {
		if(!$this->_isAjax) {
			$this->setSmartyBacklink();
			$this->_smarty->append('error', $msg);
			$this->display();
			die();
		}
		else {
			die(json_encode(array(
				'val' => 'error',
				'msg' => $msg . $this->createButtons()
			)));
		}
	}

	public function dieMessage($msg) {
		if(!$this->_isAjax) {
			$this->setSmartyBacklink();
			$this->_smarty->append('message', $msg);
			$this->display();
			die();
		}
		else {
			die(json_encode(array(
				'val' => 'message',
				'msg' => $msg . $this->createButtons()
			)));
		}
	}

	public function dieSuccess($msg) {
		if(!$this->_isAjax) {
			$this->setSmartyBacklink();
			$this->_smarty->append('success', $msg);
			$this->display();
			die();
		}
		else {
			die(json_encode(array(
				'val' => 'success',
				'msg' => $msg . $this->createButtons()
			)));
		}
	}

	public function showError($msg) {
		if(!$this->_isAjax) {
			$this->_smarty->append('error', $msg . '<br />');
		}
		else {
			die('Not supported with ajax!');
		}
	}

	public function showMessage($msg) {
		if(!$this->_isAjax) {
			$this->_smarty->append('message', $msg . '<br />');
		}
		else {
			die('Not supported with ajax!');
		}
	}

	public function showSuccess($msg) {
		if(!$this->_isAjax) {
			$this->_smarty->append('success', $msg . '<br />');
		}
		else {
			die('Not supported with ajax!');
		}
	}

	public function dieDisplay() {
		if(!$this->_isAjax) {
			$this->setSmartyBacklink();
			$this->display();
			die();
		}
		else {
			die('Not supported with ajax!');
		}
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

	/**
	 * Creates
	 * @return [type] [description]
	 */
	protected function createButtons() {

		$str = '';
		if(!empty($this->_buttonlinks)) {
			$str .= '<div>';
			foreach($this->_buttonlinks as $btn) {
				$str .= "<a class='btn btn-{$btn['type']}'href='{$btn['link']}'>" .
					"{$btn['name']}" . '</a>';
			}
			$str .= '</div>';
		}
		else {
			return '';
		}
		return $str;
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

	protected $_isAjax;
}

?>
