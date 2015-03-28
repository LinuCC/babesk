<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Fits/Fits.php';

class Fmenu extends Fits {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $smartyPath;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->smartyPath = PATH_SMARTY_TPL . '/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//No direct access
		defined('_WEXEC') or die("Access denied");

		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/FitsManager.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';

		$this->entryPoint($dataContainer);
		$smarty = $dataContainer->getSmarty();
		$userManager = new UserManager();
		$fitsManager = new FitsManager();
		$gsm = new GlobalSettingsManager();

		$has_Fits=false;


		try {
			$userDetails = TableMng::querySingleEntry(sprintf(
				'SELECT u.*,
				(SELECT CONCAT(g.gradelevel, g.label) AS class
					FROM SystemAttendances uigs
					LEFT JOIN SystemGrades g ON uigs.gradeId = g.ID
					WHERE uigs.userId = u.ID AND
						uigs.schoolyearId = @activeSchoolyear) AS class
				FROM SystemUsers u WHERE `ID` = %s', $_SESSION['uid']), true);
			$userClass = $userDetails['class'];
			$fitsManager->prepUser($_SESSION['uid']);
			$has_Fits = $fitsManager->getFits($_SESSION['uid']);
			$class = $gsm->getFitsClass();
			$allClasses = $gsm->getFitsAllClasses();
		} catch (Exception $e) {
			$this->_logger->log(
				'Error executing Fits: ' . $e->getMessage(), 'Notice', Null, ''
			);
			$this->_interface->dieError('Konnte Fits nicht ausführen.');
		}

		if ($allClasses==true) {
			$userClass =  preg_replace('/[^0-9]/i', '', $userClass);
			$class =  preg_replace('/[^0-9]/i', '', $class);
		}

		if (isset($userClass) && $userClass==$class && $has_Fits == false) {
			$smarty->assign('showTestlink', true);
		}

		if ($has_Fits==true) {
			$smarty->assign('hasFits',true);
		}
		$smarty->assign('uid', $_SESSION['uid']);
		$smarty->display($this->smartyPath . 'menu.tpl');
	}
}
?>
