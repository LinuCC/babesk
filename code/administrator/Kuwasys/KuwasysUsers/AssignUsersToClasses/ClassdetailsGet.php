<?php

namespace administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses;

require_once __DIR__ . '/AssignUsersToClasses.php';

/**
 * Allows JS to fill its tables with the Data
 */
class ClassdetailsGet extends \administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);

		try {
			$data = $this->temporaryAssignmentsRequestsOfClassGet(
				$_POST['classId'], $_POST['categoryId']
			);
			$data = $this->requestsAtSameCategoryForRequestsAppend($data);

		} catch(PDOException $e) {
			$this->_logger->log('Error fetching the user-assignments',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not fetch the User-Assignments') . $e->getMessage())));
		}

		die(json_encode($data));
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches all Userrequests of a Class
	 *
	 * @param  int    $classId The ID of the Class
	 * @return array           The Userrequests
	 * @throws PDOException If Error happened when fetching the Data
	 */
	protected function temporaryAssignmentsRequestsOfClassGet(
		$classId, $categoryId
	) {

		try {

			$stmt = $this->_pdo->prepare(
				'SELECT IF(ra.statusId <> 0, uics.name, "removed") statusname,
					ra.statusId AS statusId, ra.classId AS classId,
					ra.userId AS userId, ra.categoryId AS categoryId,
					IF(origuics.ID, origuics.translatedName, "N/A")
						AS origStatusname,
					CONCAT(u.forename, " ", u.name) AS username,
					CONCAT(g.gradelevel, "-", g.label) AS grade
				FROM KuwasysTemporaryRequestsAssign ra
				INNER JOIN SystemUsers u ON ra.userId = u.ID
				LEFT JOIN SystemAttendances uigsy
					ON ra.userId = uigsy.userId
						AND uigsy.schoolyearId = @activeSchoolyear
				LEFT JOIN SystemGrades g ON uigsy.gradeId = g.ID
				LEFT JOIN KuwasysUsersInClassStatuses uics
					ON ra.statusId = uics.ID
				LEFT JOIN KuwasysClasses c ON ra.classId = c.ID
				LEFT JOIN KuwasysClassesInCategories cic
					ON cic.classId = c.ID
					AND cic.categoryId = :categoryId
				LEFT JOIN KuwasysUsersInClassStatuses origuics
					ON origuics.ID = cic.categoryId
				WHERE ra.classId = :classId AND ra.categoryId = :categoryId
			');
			$stmt->execute(array(
				'classId' => $classId, 'categoryId' => $categoryId
			));
			return $stmt->fetchAll(\PDO::FETCH_GROUP);

		} catch (\PDOException $e) {
			var_dump($e->getMessage());
			$this->_logger->log('Error fetching temporary assignments',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			http_response_code(500);
		}
	}

	/**
	 * Adds the other requests the user is in to the request
	 * @param  array  $requests The requests to display
	 * @return array            The requests with more data
	 */
	protected function requestsAtSameCategoryForRequestsAppend($requests) {

		$stmt = $this->_pdo->prepare(
			'SELECT c.ID AS classId, c.label AS label,
				cic.categoryId AS categoryId,
				IF(ra.statusId <> 0, uics.name, "removed") AS statusname
			FROM KuwasysClasses c
			INNER JOIN KuwasysTemporaryRequestsAssign ra
				ON ra.classId = c.ID
			INNER JOIN KuwasysClassesInCategories cic
				ON cic.classId = c.ID
					AND cic.categoryId = :categoryId
			LEFT JOIN KuwasysUsersInClassStatuses uics
				ON ra.statusId = uics.ID
			WHERE ra.userId = :userId AND ra.classId <> :classId
		');
		foreach($requests as &$status) {
			foreach($status as &$request) {
				$stmt->execute(array(
					'categoryId' => $request['categoryId'],
					'userId' => $request['userId'],
					'classId' => $request['classId']
				));
				$otherRequests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
				$request['otherRequests'] = $otherRequests;
			}
		}
		return $requests;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>