<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Statistics/Statistics.php';
defined('PATH_STATISTICS_CHART')
OR define('PATH_STATISTICS_CHART', realpath(dirname(__FILE__) . '/../..'));


/**
 * Analyzes data of the headmodule Message and puts them out as statistics
 *
 * @author Mirek Hancl <mirek@hancl.de>
 */
class MessageStats extends Statistics {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/administrator/' . $path;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);


		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'chooseChart':
					$this->mainpageWithChart($_POST['chartName']);
					break;
				case 'showChart':
					$this->chartSelect($_GET['chart']);
					break;
				default:
					die('Wrong action-value given');
			}
		}
		else {
			$this->mainPage();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die("Access denied");

		$this->_smarty = $dataContainer->getSmarty();
		$this->_interface = $dataContainer->getInterface();
	}

	protected function mainpageWithChart($chartName) {

		$this->_smarty->assign('chartName', $chartName);
		$this->mainPage();
	}

	protected function mainPage() {

		$this->_smarty->display($this->_smartyPath . 'main.tpl');
	}

	/**
	 * All hail the Spaghetti[Code]Monster! Yarrr!
	 * @return void
	 */
	protected function chartSelect($switch) {

		require_once PCHART_PATH . '/pDraw.class.php';
		require_once PCHART_PATH . '/pData.class.php';
		require_once PCHART_PATH . '/pImage.class.php';

		switch ($switch) {
			case 'savedCopiesByTeachers':
				require_once 'MessageStatSavedCopiesByTeacher.php';
				$chart = new MessageStatSavedCopiesByTeacher();
				break;
			default:
				die('Wrong Chart-Switch given');
				break;
		}

		$this->chartShow($chart);
	}

	/**
	 * Shows the Chart
	 * @param  Object $chart A child of StatisticChart
	 * @return void
	 */
	protected function chartShow($chart) {

		try {
			$chart->setImageWidth($this->_imgWidth);
			$chart->setImageHeight($this->_imgHeight);
			$chart->imageDraw();
			$chart->imageDisplay();

		} catch (Exception $e) {
			$this->_interface->dieError('Konnte die Statistik nicht auswerten');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_smarty;
	protected $_interface;

	protected $_imgWidth = 1000;
	protected $_imgHeight = 350;

}

?>
