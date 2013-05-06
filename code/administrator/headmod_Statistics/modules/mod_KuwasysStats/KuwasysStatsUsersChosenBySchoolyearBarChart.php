<?php

require_once PATH_STATISTICS_CHART . '/StatisticsBarChart.php';

class KuwasysStatsUsersChosenBySchoolyearBarChart extends StatisticsBarChart {

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

		$this->_heading['text'] = 'Schülerteilnahmen in Schuljahren';
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

		$this->_schoolyearData = TableMng::query(
			'SELECT COUNT(*) AS userCount, uniqueUsersPerSchoolyear.id AS id,
			uniqueUsersPerSchoolyear.label AS label
			FROM
			(SELECT sy.ID AS id, uic.UserID as userId, sy.label AS label
				FROM schoolYear sy
				JOIN jointClassInSchoolYear cisy ON cisy.SchoolYearID  = sy.ID
				JOIN jointUsersInClass uic ON uic.ClassID = cisy.ClassID
				WHERE
					uic.statusId = (
						SELECT ID FROM usersInClassStatus uics
						WHERE name = "active"
					)
					OR
					uic.statusId = (
						SELECT ID FROM usersInClassStatus uics
						WHERE name = "waiting"
					)
				GROUP BY uic.userId, sy.ID
				) uniqueUsersPerSchoolyear
			GROUP BY uniqueUsersPerSchoolyear.id', true);
	}

	protected function dataProcess() {

		$names = array();
		$data = array();
		$this->_pData = new pData();

		foreach($this->_schoolyearData as $schoolyear) {
			$names[] = $schoolyear['label'];
			$data[] = $schoolyear['userCount'];
		}

		$this->_pData->addPoints($data, 'wählende Benutzer');
		$this->_pData->addPoints($names, 'schoolyearLabels');
		$this->_pData->setSerieDescription('schoolyearLabels', 'Schuljahr');
		$this->_pData->setAbscissa('schoolyearLabels');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_schoolyearData;
}

?>