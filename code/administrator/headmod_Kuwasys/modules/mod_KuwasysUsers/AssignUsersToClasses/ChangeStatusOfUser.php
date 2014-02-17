<?php

namespace administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses;

require_once __DIR__ . '/AssignUsersToClasses.php';

/**
 * Allows the Admin to change the Status of UserToClass-Assignments
 */
class ChangeStatusOfUser extends \administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);

		try {
			$statusId = ($_POST['statusname'] != 'removed') ?
				$this->statusIdGetByNameWoException($_POST['statusname']) : 0;

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not fetch the Status'))));
		}

		$this->changeStatus($_POST['userId'], $_POST['classId'], $statusId);

		die(json_encode(array('value' => 'success',
			'message' => _g('The Status of the User was successfully changed')
		)));
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Changes the Status of a Temporary Request Entry
	 *
	 * Dies with Json on Error
	 */
	private function changeStatus($userId, $classId, $statusId) {

		try {
			$stmt = $this->_pdo->prepare('UPDATE KuwasysTemporaryRequestsAssign
				SET statusId = :statusId
				WHERE classId = :classId AND userId = :userId');

			$stmt->execute(array(
				'statusId' => $statusId,
				'classId' => $classId,
				'userId' => $userId
			));

		} catch (PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not change the Status of the User'))));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>