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
			'SELECT COUNT(*) AS gradelevelCount, gradeValue AS gradelevel
			FROM grade g
				JOIN usersInGradesAndSchoolyears uigs ON g.ID = uigs.GradeID
					AND uigs.schoolyearId = @activeSchoolyear
				JOIN jointUsersInClass uic ON uic.UserID = uigs.UserID
				JOIN jointClassInSchoolYear cisy
					ON uic.ClassID = cisy.ClassID
					AND cisy.SchoolYearID = @activeSchoolyear
				JOIN (
						SELECT ID
						FROM usersInClassStatus
						WHERE name="active" OR name="waiting"
					) status ON status.ID = uic.statusId
				GROUP BY g.gradeValue
			', true);
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
