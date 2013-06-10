<?php

require_once PATH_STATISTICS_CHART . '/StatisticsBarChart.php';

class BabeskStatTopMealsByMonthBarChart extends StatisticsBarChart {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function imageDraw($externalData = NULL) {

		$this->_heading['text'] = 'Beliebteste Mahlzeiten pro Monat';
		$this->setMarginRatio(array('X' => 0.15, 'Y' => 0.15));
		$this->setScale(array("GridR" => 200, "GridG" => 200,"GridB" => 200,
			"DrawSubTicks" => TRUE, 'LabelRotation'  =>  45,
			"Pos" => SCALE_POS_TOPBOTTOM, "Mode" => SCALE_MODE_ADDALL_START0));
		parent::imageDraw();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function dataFetch() {

		$this->_mealData = TableMng::query(
			'SELECT SUM(changed_cardID) AS summe FROM cards', true);
	}

	protected function dataProcess() {

		$names = array();
		$this->_pData = new pData();

// 		foreach($this->_mealData as $schoolyear) {
// 			$names[] = $schoolyear['summe'];
		
// 		}

		
		$this->_pData->addPoints($this->_mealData[0], 'schoolyearLabels');
// 		$this->_pData->setSerieDescription('schoolyearLabels', 'Schuljahr');
// 		$this->_pData->setAbscissa('schoolyearLabels');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_mealData;
}

?>