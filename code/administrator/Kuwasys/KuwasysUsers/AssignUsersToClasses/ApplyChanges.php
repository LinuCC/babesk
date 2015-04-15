<?php

namespace administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses;

require_once __DIR__ . '/AssignUsersToClasses.php';

/**
 * Applies the changes temp. made to the UsersInClass-Table
 */
class ApplyChanges extends \administrator\Kuwasys\KuwasysUsers\AssignUsersToClasses {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);

		$this->_pdo->beginTransaction();
		$this->usersInClassJointDeleteByNewAssignments();
		$this->newAssignmentsAddToJoints();
		$this->_pdo->commit();
		$this->_interface->backlink('administrator|Kuwasys|KuwasysUsers|' .
			'AssignUsersToClasses');
		$this->_interface->dieSuccess(_g(
			'The users were successfully assigned to their respective classes!'
		));
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Deletes all UsersInClass-Joints which got changed by the new Assignments
	 *
	 * Dies displaying a Message on Error
	 */
	protected function usersInClassJointDeleteByNewAssignments() {

		try {
			$this->_pdo->exec('DELETE uic.*
				FROM KuwasysUsersInClassesAndCategories uic
				JOIN KuwasysTemporaryRequestsAssign ra
					ON uic.ClassID = ra.origClassId
						AND uic.userId = ra.origUserId
						AND uic.categoryId = ra.origCategoryId
			');

		} catch (PDOException $e) {
			$this->_interface->dieError(
				_g('Could not delete the old Joints!'));
		}
	}

	/**
	 * Adds the temporary assignments to the jointUsersInClass-Table
	 *
	 * Dies displaying a Message on Error.
	 * Only Entries with an StatusId that is not Zero will be added.
	 */
	protected function newAssignmentsAddToJoints() {

		try {
			$this->_pdo->exec('INSERT INTO KuwasysUsersInClassesAndCategories
				(UserID, ClassID, statusId, categoryId)
				SELECT userId, classId, statusId, categoryId
					FROM KuwasysTemporaryRequestsAssign re
					WHERE statusId <> 0');

		} catch (PDOException $e) {
			$this->_interface->dieError(_g('Could not add the new Joints!') . $e->getMessage());
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////


}

?>