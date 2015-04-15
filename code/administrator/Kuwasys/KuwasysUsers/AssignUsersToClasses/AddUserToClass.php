<?php

namespace administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses;

require_once __DIR__ . '/AssignUsersToClasses.php';

/**
 * Allows the Admin to Add a User to the Class to the Temp-Table
 */
class AddUserToClass extends \administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);

		$userId = $this->userIdGetByUsername($_POST['username']);
		$statusId = $this->statusIdGetByName($_POST['statusname']);
		$this->userAssignToClass(
			$userId, $_POST['classId'], $_POST['categoryId'], $statusId
		);

		die(json_encode(array('value' => 'success',
			'message' => _g('The User was successfully added'))));
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Assigns the User to a Class
	 *
	 * @param  int    $userId   The ID of the User to assign
	 * @param  int    $classId  The ID of the Class to assign the User to
	 * @param  int    $statusId The ID of the Status
	 * @throws PDOException If Things didnt work out
	 */
	private function userAssignToClass(
		$userId, $classId, $categoryId, $statusId
	) {

		try {
			$stmt = $this->_pdo->prepare(
				'INSERT INTO KuwasysTemporaryRequestsAssign
				(userId, classId, categoryId, statusId, origUserId,
					origClassId, origCategoryId, origStatusId)
				VALUES
				(:userId, :classId, :categoryId, :statusId, 0, 0, 0, 0)');

			$stmt->execute(array(
				'userId' => $userId,
				'classId' => $classId,
				'categoryId' => $categoryId,
				'statusId' => $statusId,
			));

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not add the User to the Class'))));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>