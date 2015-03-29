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
					INNER JOIN KuwasysClassesInCategories cic
						ON cic.classId = c.ID
					INNER JOIN KuwasysClassCategories cc
						ON cc.ID = cic.categoryId
					WHERE sy.ID = ? AND cic.categoryId = ?
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

		$link = $this->_em
			->find('DM:UserInClassAndCategory', $_POST['joinId']);
		$doSwitchClass = $_POST['switchClass'] != 'false';
		$status = $this->_em
			->getReference('DM:UserInClassStatus', $_POST['statusId']);
		$link->setStatus($status);
		if($doSwitchClass) {
			$class = $this->_em
				->getReference('DM:KuwasysClass', $_POST['classId']);
			$link->setClass($class);
		}
		$this->_em->persist($link);
		$this->_em->flush();
		$this->_interface->dieAjax(
			'success', 'Der Status wurde erfolgreich verändert.'
		);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>