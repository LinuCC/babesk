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
				(SELECT COUNT(*) FROM KuwasysUsersInClasses uic
					INNER JOIN SystemSchoolyear sy ON sy.active = 1
					INNER JOIN class c ON c.ID = uic.ClassID
						AND c.schoolyearId = sy.ID
				WHERE uic.userId = u.ID) AS choiceCount
			FROM SystemUsers u
				INNER JOIN usersInGradesAndSchoolyears uigs
					ON u.ID = uigs.userId
					AND uigs.schoolyearId = @activeSchoolyear
				INNER JOIN SystemGrades g ON g.ID = uigs.gradeId
				INNER JOIN SystemSchoolyear sy ON uigs.SchoolYearID = sy.ID
		');
	}

	protected function schooltypeDataFetch() {

		$this->_schooltypeData = TableMng::query(
			'SELECT * FROM SystemSchooltype');
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

		if($schooltypeId == 0) {
			return _g('No ST');
		}

		foreach($this->_schooltypeData as $data) {
			if($data['ID'] == $schooltypeId) {
				return $data['token'];
			}
		}
		throw new Exception('Could not process the Schooltype-Name :' .
			$schooltypeId);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_userData;

	protected $_schooltypeData;
}

?>
