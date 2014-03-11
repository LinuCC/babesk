<?php

require_once PATH_STATISTICS_CHART . '/StatisticsStackedBarChart.php';

class KuwasysStatsSchooltypeSchoolyearChosenBarChart
	extends StatisticsStackedBarChart {

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

		$this->_heading['text'] =
			'Wahlen in Schultypen und Jahrgänge aufgeteilt';
		$this->_scale['Mode'] = SCALE_MODE_ADDALL_START0;
		parent::imageDraw();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function dataFetch() {

		TableMng::query('SET @activeSy := (SELECT ID FROM SystemSchoolyears WHERE active = "1");');

		$this->_data = TableMng::query(
			'SELECT st.ID AS schooltypeId, st.token AS schooltypeToken,
			g.gradelevel, userChosen.classesChosen, COUNT(*) AS userCount
			FROM SystemGrades g
				-- SystemSchooltypes (optional, splits gradelevels in schooltypes if
				-- needed)
				LEFT JOIN SystemSchooltypes st ON g.schooltypeId = st.ID
				-- Fetch how many classes the user has chosen
				INNER JOIN (
					SELECT COUNT(*) AS classesChosen, uigs.gradeId AS GradeID
					FROM SystemUsersInGradesAndSchoolyears uigs
						INNER JOIN KuwasysUsersInClasses uic
							ON uigs.userId = uic.UserID
						-- Check for interesting status
						INNER JOIN (
							SELECT ID
							FROM KuwasysUsersInClassStatuses
							WHERE name="active"
							) status ON status.ID = uic.statusId
					WHERE uigs.schoolyearId = @activeSchoolyear
					GROUP BY uic.UserID
					) userChosen ON userChosen.GradeID = g.ID
			GROUP BY st.ID, g.gradelevel, userChosen.classesChosen');
	}

	protected function dataProcess() {

		$endData = array();
		$endDataNames = array();
		$this->_pData = new pData();
		$sortedAndCompleteData = $this->templateArrayCreate();

		foreach($this->_data as $data) {
			$sortedAndCompleteData[$data['schooltypeToken']][$data['gradelevel']]
				[$data['classesChosen']] = (int) $data['userCount'];
		}

		foreach($sortedAndCompleteData as $schooltype => $stElements) {
			foreach($stElements as $yeargroup => $ygElements) {
				foreach($ygElements as $count => $data) {
					$countname = sprintf('%s Wahl(-en)', $count);
					$endData [$countname][] = $data;
					$endDataNames [$schooltype . ' ' .  $yeargroup] = $schooltype . ' ' . $yeargroup;
				}
			}
		}

		foreach ($endData as $countname => $userCounts) {
			$this->_pData->addPoints($userCounts, (string) $countname);
		}
		$this->_pData->addPoints($endDataNames, 'descr');
		$this->_pData->setSerieDescription
			('descr', 'Schultyp und Jahrgang');
		$this->_pData->setAbscissa('descr');
	}

	/**
	 * Creates a Array containing every possible agegroup-Schooltype-combination
	 * filled with 0. Prevents errornous data-Output when converting the Data to
	 * pChart-Data
	 * @return [type] [description]
	 */
	protected function templateArrayCreate() {

		$tplArray = array();
		$yeargroups = $this->yeargroupRangeGet();
		$schooltypes = $this->schooltypeRangeGet();
		$classCount = $this->classCountRangeGet();

		foreach($schooltypes as $schooltype => $set) {
			foreach($yeargroups as $yeargroup => $ySet) {
				foreach($classCount as $count => $cSet) {
					$tplArray[$schooltype][$yeargroup][$count] = 0;
				}
			}
		}

		return $tplArray;
	}

	/**
	 * Returns an array containing an element for every existing yeargroup
	 * @return Array
	 */
	protected function yeargroupRangeGet() {

		$yeargroups = array();

		foreach($this->_data as $data) {
			$yeargroups[$data['gradelevel']] = true;
		}
		ksort($yeargroups);

		return $yeargroups;
	}

	/**
	 * Returns an array containing an element for every used schooltype
	 * @return Array
	 */
	protected function schooltypeRangeGet() {

		$schooltypes = array();

		foreach($this->_data as $data) {
			$schooltypes[$data['schooltypeToken']] = true;
		}
		//dont sort, identifier is string => no need

		return $schooltypes;
	}

	/**
	 * Returns an array containing elements for every classCount existing in the
	 * raw data
	 * @return Array
	 */
	protected function classCountRangeGet() {

		$classCount = array();

		foreach($this->_data as $data) {
			$classCount[$data['classesChosen']] = true;
		}
		ksort($classCount);

		return $classCount;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_data;
}

?>
