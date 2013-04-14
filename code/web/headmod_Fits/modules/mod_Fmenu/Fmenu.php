<?php

require_once PATH_INCLUDE . '/Module.php';

class Fmenu extends Module {

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

		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/FitsManager.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';

		global $smarty;
		$userManager = new UserManager();
		$fitsManager = new FitsManager();
		$gsm = new GlobalSettingsManager();

		$has_Fits=false;


		try {
			$userDetails = TableMng::query(sprintf(
				'SELECT u.*,
				(SELECT CONCAT(g.gradeValue, g.label) AS class
					FROM jointUsersInGrade uig
					LEFT JOIN grade g ON uig.gradeId = g.ID
					LEFT JOIN jointGradeInSchoolYear gisy
						ON gisy.gradeId = g.ID
					LEFT JOIN schoolYear sy ON gisy.schoolyearId = sy.ID
					WHERE uig.userId = u.ID) AS class
				FROM users u WHERE `ID` = %s', $_SESSION['uid']), true);
			// $userDetails = $userManager->getUserDetails($_SESSION['uid']);
			$userClass = $userDetails[0]['class'];
		} catch (Exception $e) {
			die('Ein Fehler ist aufgetreten:'.$e->getMessage());
		}

		try {
			$fitsManager->prepUser($_SESSION['uid']);
		} catch (Exception $e) {
				die('Ein Fehler ist aufgetreten:'.$e->getMessage());
			}

		try {
			$has_Fits = $fitsManager->getFits($_SESSION['uid']);
		} catch (Exception $e) {
			die('Ein Fehler ist aufgetreten:'.$e->getMessage());
		}

		try {
			$class = $gsm->getFitsClass();
		} catch (Exception $e) {
			die('Ein Fehler ist aufgetreten:'.$e->getMessage());
		}

		try {
			$allClasses = $gsm->getFitsAllClasses();
		} catch (Exception $e) {
			die('Ein Fehler ist aufgetreten:'.$e->getMessage());
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