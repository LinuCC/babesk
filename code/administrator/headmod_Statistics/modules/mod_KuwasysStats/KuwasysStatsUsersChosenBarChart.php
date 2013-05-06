<?php

require_once PATH_STATISTICS_CHART . '/StatisticsStackedBarChart.php';

class KuwasysStatsUsersChosenStackedBarChart extends StatisticsStackedBarChart {

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
	 * Fetches the Data from the server
	 *
	 * @throws  MySQLVoidDataExcpetion If no data was fetched
	 * @throws  Exception If something has gone wrong while fetching the data
	 * @return void
	 */
	protected function dataFetch() {

		$this->userDataFetch();
		$this->schooltypeDataFetch();
	}

	protected function userDataFetch() {

		$this->_userData = TableMng::query(
			'SELECT g.schooltypeId AS schooltypeId,
				(SELECT COUNT(*) FROM jointUsersInClass uic
					JOIN jointClassInSchoolYear cisy ON uic.ClassID = cisy.ClassID
					JOIN schoolYear sy ON cisy.SchoolYearID = sy.ID
				WHERE uic.userId = u.ID AND sy.active = "1") AS choiceCount
			FROM users u
				JOIN jointUsersInSchoolYear uisy ON u.ID = uisy.UserID
				JOIN schoolYear sy ON uisy.SchoolYearID = sy.ID
				JOIN jointUsersInGrade uig ON u.ID = uig.UserID
				JOIN grade g ON g.ID = uig.GradeID
				JOIN jointGradeInSchoolYear gisy ON gisy.GradeID = g.ID
				JOIN schoolYear sy_g ON gisy.SchoolYearID = sy_g.ID
			WHERE
				sy.active = "1" AND sy_g.active = "1"
				', true);
	}

	protected function schooltypeDataFetch() {

		$this->_schooltypeData = TableMng::query(
			'SELECT * FROM Schooltype', true);
	}

	protected function dataProcess() {

		$data = array();
		$this->_pData = new pData();

		foreach($this->_userData as $user) {
			if($user['choiceCount'] == 0) {
				$poolName = 'nicht gewählt';
			}
			else {
				$poolName = 'gewählt';
			}
			if(isset($data[$poolName][$user['schooltypeId']])) {
				$data[$poolName][$user['schooltypeId']] += 1;
			}
			else {
				$data[$poolName][$user['schooltypeId']] = 1;
			}
		}


		$schooltypes = $this->dataProcessSchooltypes($data);


		foreach($data as $poolName => $pool) {
			$values = array();
			foreach($schooltypes as $schooltypeId => $schooltypeName) {
				if(isset($pool[$schooltypeId])) {
					$values[] = $pool[$schooltypeId];
				}
				else {
					$values[] = 0;
				}
			}
			$this->_pData->addPoints($values, $poolName);
		}

		$this->_pData->addPoints($schooltypes, 'schooltypes');
		$this->_pData->setSerieDescription('schooltypes', 'Schultypen');
		$this->_pData->setAbscissa('schooltypes');
	}

	/**
	 * Counts all Schooltypes so that we dont end with a wrong Bar Chart, when
	 * all users of one Schooltype chose / notChose classes
	 *
	 * @param Array $data expects an Array of presorted data
	 * @return Array An Array with all Schooltypes in it
	 */
	protected function dataProcessSchooltypes($data) {

		$schooltypes = array();
		foreach($data as $pool) {
			foreach($pool as $schooltypeId => $schooltypeCount) {
				if(!isset($schooltypes[$schooltypeId])) {
					$schooltypes[$schooltypeId] =
						$this->dataProcessSchooltypeNameGet($schooltypeId);
				}
			}
		}
		return $schooltypes;
	}

	protected function dataProcessSchooltypeNameGet($schooltypeId) {

		foreach($this->_schooltypeData as $data) {
			if($data['ID'] == $schooltypeId) {
				return $data['name'];
			}
		}
		throw new Exception('Could not process the Schooltype-Name' . $schooltypeId);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_userData;

	protected $_schooltypeData;
}

?>