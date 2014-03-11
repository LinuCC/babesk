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
				$_POST['classId']);

		} catch(PDOException $e) {
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
	protected function temporaryAssignmentsRequestsOfClassGet($classId) {

		$stmt = $this->_pdo->prepare(
			'SELECT IF(ra.statusId <> 0, uics.name, "removed") statusname,
				ra.statusId AS statusId, ra.classId AS classId,
				ra.userId AS userId,
				IF(origuics.ID, origuics.translatedName, "N/A") AS origStatusname,
				CONCAT(u.forename, " ", u.name) AS username,
				CONCAT(g.gradelevel, "-", g.label) AS grade,
				(SELECT c2.ID FROM KuwasysClasses c2
					JOIN KuwasysTemporaryRequestsAssign ra2
						ON ra2.classId = c2.ID
					WHERE c2.unitId = c.unitId AND ra2.userId = ra.userId AND ra2.classId <> ra.classId
				) AS otherClassId,
			(SELECT c2.label FROM KuwasysClasses c2
				JOIN KuwasysTemporaryRequestsAssign ra2
					ON ra2.classId = c2.ID
				WHERE c2.unitId = c.unitId AND ra2.userId = ra.userId AND ra2.classId <> ra.classId
			) AS otherClassLabel
			FROM KuwasysTemporaryRequestsAssign ra
			JOIN SystemUsers u ON ra.userId = u.ID
			LEFT JOIN usersInGradesAndSchoolyears uigsy
				ON ra.userId = uigsy.userId
					AND uigsy.schoolyearId = @activeSchoolyear
			LEFT JOIN SystemGrades g ON uigsy.gradeId = g.ID
			LEFT JOIN KuwasysUsersInClassStatuses uics ON ra.statusId = uics.ID
			LEFT JOIN KuwasysClasses c ON ra.classId = c.ID
			LEFT JOIN KuwasysUsersInClassStatuses origuics
				ON ra.origStatusId = origuics.ID
			WHERE ra.classId = :classId
		');

		$stmt->execute(array('classId' => $classId));

		return $stmt->fetchAll(\PDO::FETCH_GROUP);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>