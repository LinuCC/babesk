<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Fits/Fits.php';

class Quiz extends Fits {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $smartyPath;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//No direct access
		defined('_WEXEC') or die("Access denied");

		global $smarty;

		require_once PATH_ACCESS . '/GlobalSettingsManager.php';
		require_once PATH_ACCESS . '/FitsManager.php';

		$gsm = new GlobalSettingsManager();
		$fm = new FitsManager();
		$has_Fits = false;

		if (isset($_POST['fits_key'])) {
			try {
				if ($_POST['fits_key'] == $gsm->getFitsKey()) {
					$has_Fits = true;

					$fm->setFits($_SESSION['uid'],true,$gsm->getFitsYear());

				} else {
					$smarty->display($this->smartyPath . 'quiz_error.tpl');
				}
			} catch (Exception $e) {
			}
		}


		$smarty->assign('uid',$_SESSION['uid']);
		if ($has_Fits) {
			$smarty->display($this->smartyPath . 'quiz_success.tpl');
		} else {
			$smarty->display($this->smartyPath . 'quiz.tpl');
		}
	}
}
?>
