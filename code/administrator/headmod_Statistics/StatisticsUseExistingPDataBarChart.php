<?php

require_once PATH_STATISTICS_CHART . '/StatisticsBarChart.php';

/**
 * This Class draws a BarChart using pData
 *
 * This class expects that the pData-Object is set with setPData() before the
 * Image gets drawn. The reason is that it allows to fetch a single Datablock
 * for creating multiple Charts in one go, saving Database-Traffic
 */
class StatisticsUseExistingPDataBarChart extends StatisticsBarChart {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the class
	 */
	public function __construct($data) {

		$this->setPData($data);
		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * The data is already given, we dont need this function
	 * @return void
	 */
	protected function dataFetch() {

	}

	/**
	 * The data is already given, we dont need this function
	 * @return void
	 */
	protected function dataProcess() {

	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>
