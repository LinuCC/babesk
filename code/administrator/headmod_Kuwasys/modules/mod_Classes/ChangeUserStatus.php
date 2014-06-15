<?php

namespace administrator\Kuwasys\Classes;

require_once 'Classes.php';

/**
 * Changes the users status of a class or moves him to another class
 */
class ChangeUserStatus extends \Classes {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_GET['fetchclasses'], $_POST['schoolyearId'])) {
			$this->classesAjax();
		}
		else {
			$this->statusChange();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Sends data to the client containing classes of this schoolyear
	 */
	private function classesAjax() {

		$classes = $this->classesKeyPairFetch();
		if($classes) {
			$this->_interface->dieAjax('success', $classes);
		}
		else {
			$this->_interface->dieAjax(
				'error', 'Ein Fehler ist beim Holen der Kurse aufgetreten.'
			);
		}
	}

	/**
	 * Fetches the classes of the schoolyear as Key-Pair
	 * @param  int    $syId The id of the schoolyear of the classes
	 * @return array        an array containing the id as the array-key and the
	 *                      name of the class as value
	 */
	private function classesKeyPairFetch() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT c.ID, c.label FROM KuwasysClasses c
					INNER JOIN SystemSchoolyears sy ON sy.ID = c.schoolyearId
					INNER JOIN KuwasysClassCategories cc ON cc.ID = c.unitId
					WHERE sy.ID = ? AND c.unitId = ?
			');
			$stmt->execute(
				array($_POST['schoolyearId'], $_POST['categoryId'])
			);
			return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

		} catch (\PDOException $e) {
			$this->_logger->log('error fetching the key-pair classes of a ' .
				'schoolyear', 'Notice', Null,
				json_encode(array(
					'msg' => $e->getMessage(),
					'schoolyearId' => $syId
			)));
			return false;
		}
	}

	/**
	 * Changes the status of the user-class link
	 */
	private function statusChange() {

		if(!$_POST['switchClass']) {
			$changed = $this->joinChange();
		}
		else {
			$changed = $this->joinWithClassChange();
		}
		if($changed) {
			$this->_interface->dieAjax(
				'success', 'Der Status wurde erfolgreich verändert.'
			);
		}
		else {
			$this->_logger->log(
				'error changing the usersInClass-entry; Nothing changed',
				'Notice', Null, json_encode(array(
						'joinId' => $_POST['joinId']
			)));
			$this->_interface->dieAjax(
				'error', 'Nichts wurde verändert.'
			);
		}
	}

	/**
	 * Changes the status of the user-class link in the database
	 * @return bool   true if a row was changed, false if not
	 */
	private function joinChange() {

		try {
			$stmt = $this->_pdo->prepare(
				'UPDATE KuwasysUsersInClasses uic
					SET statusId = ?
					WHERE ID = ?
			');
			$stmt->execute(array($_POST['statusId'], $_POST['joinId']));
			return (boolean) $stmt->rowCount();

		} catch (\PDOException $e) {
			$this->_logger->log('error changing the status',
				'Moderate', Null, json_encode(array(
					'msg' => $e->getMessage()
			)));
			$this->_interface->dieAjax(
				'error', 'Fehler beim Verändern des Statuses'
			);
		}
	}

	/**
	 * Changes the status and class of the user-class link
	 * @return bool   true if a row was changed, false if not
	 */
	private function joinWithClassChange() {

		try {
			$stmt = $this->_pdo->prepare(
				'UPDATE KuwasysUsersInClasses uic
					SET statusId = ?, ClassID = ?
					WHERE ID = ?
			');
			$stmt->execute(
				array($_POST['statusId'], $_POST['classId'], $_POST['joinId'])
			);
			return (boolean) $stmt->rowCount();

		} catch (\PDOException $e) {
			$this->_logger->log('error changing the status and class',
				'Moderate', Null, json_encode(array(
					'msg' => $e->getMessage()
			)));
			$this->_interface->dieAjax(
				'error', 'Fehler beim Verändern des Statuses'
			);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>