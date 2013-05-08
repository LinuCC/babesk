<?php

require_once 'StatisticsChart.php';

/**
 * BaseClass to create Stacked BarCharts with pChart
 *
 * @author  Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
abstract class StatisticsStackedBarChart extends StatisticsChart {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Draws the Bar-Chart onto the Image
	 * @return void
	 */
	protected function imageChartDraw() {

		$this->_pImage->drawStackedBarChart($this->_stackedBarChart);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_stackedBarChart = array(
		'Surrounding' => -30,
		'InnerSurrounding' => 30,
		'Interleave' => 0.1,
		'DisplayValues' => true,
		'DisplayPos' => LABEL_POS_INSIDE);
}

?>