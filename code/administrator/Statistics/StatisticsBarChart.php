<?php

require_once 'StatisticsChart.php';

/**
 * BaseClass to create BarCharts with pChart
 */
abstract class StatisticsBarChart extends StatisticsChart {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns the Settings of the BarChart
	 *
	 * @return Array The Settings of the BarChart
	 */
	public function getBarChart() {
		return $this->_barChart;
	}

	/**
	 * Sets the Settings of the BarChart
	 *
	 * @param Array $barChart The Settings of the BarChart
	 */
	public function setBarChart($barChart) {
		$this->_barChart = $barChart;
		return $this;
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Draws the Bar-Chart onto the Image
	 * @return void
	 */
	protected function imageChartDraw() {

		$this->_pImage->drawBarChart($this->_barChart);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Settigns defining the way the barChart gets drawn
	 * @var array
	 */
	protected $_barChart = array(
		'Surrounding' => -30,
		'InnerSurrounding' => 30,
		'Interleave' => 0.1,
		'DisplayValues' => true,
		'DisplayPos' => LABEL_POS_INSIDE);


}

?>
