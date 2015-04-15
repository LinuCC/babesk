<?php

namespace administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses;

require_once __DIR__ . '/AssignUsersToClasses.php';

/**
 * Allows the Admin to change the Class of a UserToClass-Assignment
 */
class ChangeClassOfUser extends \administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);

		die(json_encode(array('value' => 'error',
			'message' => json_encode($_POST)
		)));
		$this->classChange(
			$_POST['userId'], $_POST['classId'], $_POST['newClassId'],
			$_POST['newClassCategoryId']
		);
		die(json_encode(array('value' => 'success',
			'message' => _g('The User was successfully moved.'))));
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	private function classChange(
		$userId, $classId, $newClassId, $newCategoryId
	) {

		try {
			$stmt = $this->_pdo->prepare('UPDATE KuwasysTemporaryRequestsAssign
				SET classId = :newClassId, categoryId = :newCategoryId
				WHERE userId = :userId AND classId = :classId');

			$stmt->execute(array(
				'userId' => $userId,
				'classId' => $classId,
				'newClassId' => $newClassId,
				'newCategoryId' => $newCategoryId
			));

		} catch (\PDOException $e) {
			die(json_encode(array('value' => 'error',
				'message' => _g('Could not move the User to the other Class!')
			)));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>