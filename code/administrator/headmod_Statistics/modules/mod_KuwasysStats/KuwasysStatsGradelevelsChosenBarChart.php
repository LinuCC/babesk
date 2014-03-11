<?php

require_once PATH_STATISTICS_CHART . '/StatisticsBarChart.php';

class KuwasysStatsGradelevelsChosenBarChart extends StatisticsBarChart {

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

		$this->_heading['text'] = 'Wahlen in Jahrgänge aufgeteilt';
		parent::imageDraw();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function dataFetch() {

		$this->_gradeData = TableMng::query(
			'SELECT COUNT(uic.ID) AS gradelevelCount, gradelevel AS gradelevel
			FROM SystemGrades g
				INNER JOIN usersInGradesAndSchoolyears uigs
					ON g.ID = uigs.gradeId
					AND uigs.schoolyearId = @activeSchoolyear
				INNER JOIN KuwasysUsersInClasses uic ON uic.UserID = uigs.userId
				INNER JOIN class c ON c.ID = uic.ClassID
					AND c.schoolyearId = @activeSchoolyear
				INNER JOIN (
						SELECT ID
						FROM usersInClassStatus
						WHERE name="active"
					) status ON status.ID = uic.statusId
				GROUP BY g.gradelevel
			');
	}

	protected function dataProcess() {

		$names = array();
		$data = array();
		$this->_pData = new pData();

		foreach($this->_gradeData as $grade) {
			$names[] = $grade['gradelevel'];
			$data[] = $grade['gradelevelCount'];
		}

		$this->_pData->addPoints($data, 'Wahlen');
		$this->_pData->addPoints($names, 'gradelevelNames');
		$this->_pData->setSerieDescription('gradelevelNames', 'Jahrgang');
		$this->_pData->setAbscissa('gradelevelNames');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_gradeData;
}

?>
