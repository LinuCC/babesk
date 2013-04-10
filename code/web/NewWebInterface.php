<?php

class NewWebInterface {
	////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////
	public static function init($smarty = NULL) {
		self::$_smarty = $smarty;
	}

	////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////

	/**
	 * [getSmarty description]
	 * @return [type] [description]
	 */
	public static function getSmarty() {
		return self::$_smarty;
	}

	/**
	 * @param Smarty $smarty the smarty-variable to set
	 */
	public static function setSmarty($smarty) {
		self::$_smarty = $smarty;
	}

	/**
	 * Sets the Backlink-Variable. It is used for showing a "Back" (German:
	 * "zuruck") - Button when the Script crashes and uses one of the
	 * die*()-Functions of this class
	 * @param string $backlink contains only the content of the href! A simple
	 * link without Html.
	 */
	public static function setBacklink($backlink) {
		self::$_backlink = $backlink;
	}

	////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////

	/**
	 * Parses and displays the Content of the file $fileLink within the normal Webpage-Layout and dies
	 * @param string $fileLink The Link
	 */
	public static function dieContent($fileLink) {
		self::setSmartyBacklink();
		$content = self::$_smarty->fetch($fileLink);
		self::$_smarty->assign('content', $content);
		self::display();
		die();
	}

	public static function dieError($msg) {
		self::setSmartyBacklink();
		self::$_smarty->append('error', $msg);
		self::display();
		die();
	}

	public static function dieMessage($msg) {
		self::setSmartyBacklink();
		self::$_smarty->append('message', $msg);
		self::display();
		die();
	}

	public static function showError($msg) {
		self::$_smarty->append('error', $msg . '<br />');
	}

	public static function showMessage($msg) {
		self::$_smarty->append('message', $msg . '<br />');
	}

	public static function dieDisplay() {
		self::setSmartyBacklink();
		self::display();
		die();
	}

	////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////

	/**
	 * Sets the Smarty-Backlink-Variable
	 */
	protected static function setSmartyBacklink() {
		if(isset(self::$_backlink)) {
			self::$_smarty->assign('backlink', self::$_backlink);
		}
	}

	/**
	 * Displays the now-used Template-file with Smarty
	 * @return [type] [description]
	 */
	protected static function display() {
		self::$_smarty->display('web/baseLayout.tpl');
	}

	////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////

	protected static $_smarty;

	/**
	 * A link back, usable for example if the program dies with an error
	 * @var [type]
	 */
	protected static $_backlink;
}

?>