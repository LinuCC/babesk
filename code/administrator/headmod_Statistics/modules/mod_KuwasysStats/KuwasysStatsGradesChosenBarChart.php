<?php

require_once PATH_STATISTICS_CHART . '/StatisticsBarChart.php';

class KuwasysStatsGradesChosenBarChart extends StatisticsBarChart {

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

		$this->_heading['text'] = 'Wahlen in Klassen aufgeteilt';
		parent::imageDraw();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function dataFetch() {

		$this->_gradeData = TableMng::queryMultiple(
			'SELECT COUNT(*) AS gradeCount, CONCAT(g.gradelevel, "-", g.label) AS gradeName
			FROM Grades g
				JOIN usersInGradesAndSchoolyears uigs ON g.ID = uigs.gradeId
					AND uigs.schoolyearId = @activeSchoolyear
				JOIN jointUsersInClass uic ON uic.UserID = uigs.userId
				JOIN jointClassInSchoolYear cisy
					ON uic.ClassID = cisy.ClassID
					AND cisy.SchoolYearID = @activeSchoolyear
				JOIN (
						SELECT ID
						FROM usersInClassStatus
						WHERE name="active" OR name="waiting"
					) status ON status.ID = uic.statusId
				GROUP BY g.ID
			');
	}

	protected function dataProcess() {

		$names = array();
		$data = array();
		$this->_pData = new pData();

		foreach($this->_gradeData as $grade) {
			$names[] = $grade['gradeName'];
			$data[] = $grade['gradeCount'];
		}

		$this->_pData->addPoints($data, 'Wahlen');
		$this->_pData->addPoints($names, 'gradeNames');
		$this->_pData->setSerieDescription('gradeNames', 'Klasse');
		$this->_pData->setAbscissa('gradeNames');
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_gradeData;
}

?>
