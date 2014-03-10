<?php

namespace web\Kuwasys\ClassList;

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/headmod_Kuwasys/modules/mod_ClassList/ClassList.php';
require_once PATH_INCLUDE . '/ArrayFunctions.php';

class UserSelectionsApply extends \web\Kuwasys\ClassList {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/**
	 * Constructs the Module
	 * @param string $name         The Name of the Module
	 * @param string $display_name The Name that should be displayed to the
	 *                             User
	 * @param string $path         A relative Path to the Module
	 */
	public function __construct ($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Executes the Module, does things based on ExecutionRequest
	 *
	 * @param  DataContainer $dataContainer contains data needed by the Module
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(empty($_POST['choices'])) {
			$this->_interface->dieError(_g('No choices made!'));
		}
		$choices = $_POST['choices'];
		$this->_selClassIds = $this->classIdsOfChoicesGet($choices);

		try {
			$err = $this->inputCheck($choices);
		} catch (\Exception $e) {
			$this->_interface->dieError(_g('Error checking the input data!'));
		}

		if(empty($err)) {
			$this->choicesUpload($choices);
			$this->_interface->dieMessage(_g(
				'Your choices where added. You can see your registrations ' .
				'in the main menu.')
			);
		}
		else {
			$this->_interface->dieError($err);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Initializes data needed by the Object
	 *
	 * @param  DataContainer $dataContainer Contains data needed by Classes
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->_interface->setBacklink(
			'index.php?module=web|Kuwasys|ClassList'
		);
		$this->_interface->addButton(
			_g('Go to Main menu'),
			'index.php?module=web|Kuwasys'
		);
	}

	/**
	 * Flattens the choices to just the classIds and returns them
	 * @param  array  $choices The choices
	 * @return array           The classIds
	 *         Structure:
	 *         '<index>' => '<class-id>'
	 */
	private function classIdsOfChoicesGet($choices) {

		$classIds = array();
		foreach($choices as $unit) {
			foreach($unit as $classId) {
				$classIds[] = $this->_pdo->quote($classId);
			}
		}
		return $classIds;
	}

	/**
	 * Checks the input the user has made
	 * @param  array  $choices The choices the user has made
	 * @return string          A void string on no error, else the errormessage
	 */
	private function inputCheck($choices) {

		$error = '';

		if(!$this->somethingSelectedCheck($choices)) {
			$error = _g(
				'You did not make any selection. Nothing will be changed.'
			);
		}
		else if(!$this->multipleSelectionsOfSameClassCheck($choices)) {
			$error = _g(
				'You can only select a class as either primary or ' .
				'secondary, not both at once!'
			);
		}
		else if(!$this->noClassesAtUnitYetChosenCheck($choices)) {
			$error = _g(
				'You have selected a class at a day at which you ' .
				'have already selected classes. Please undo your choices ' .
				'and then try again if you want to change them!'
			);
		}
		else if(!$this->globalClassRegistrationsAllowed()) {
			$error = _g(
				'Classregistrations are disabled!'
			);
		}
		else if(!$this->classesChosenRegistrationAllowedCheck($choices)) {
			$error = _g(
				'One or more classes you have chosen are not enabled!'
			);
		}
		foreach($choices as $unitChoices) {
			if(!$this->selectionTypesOfUnitChoicesCheck($unitChoices)) {
				$error = _g(
					'For a specific day you did not chose a primary ' .
					'request, but you did chose a secondary request. If you ' .
					'only have one request at this day, please select the ' .
					'class as a primary request.'
				);
			}
		}
		return $error;
	}

	/**
	 * Checks if something was selected by the user
	 * @param  array  $choices the choices of the user
	 * @return bool            true if a selection exists, false if not
	 */
	private function somethingSelectedCheck($choices) {

		$unit = \ArrayFunctions::firstValue($choices);
		return (
			$unit !== false && \ArrayFunctions::firstValue($unit) !== false
		);
	}

	/**
	 * Checks if the choice-types were correctly made
	 * Not allowed are:
	 *     Second choice without first choice
	 *     choices with other names than "request1" or "request2"
	 * @param  array  $uChoices unit-specific choices
	 * @return bool             true on no error, else false
	 */
	private function selectionTypesOfUnitChoicesCheck($uChoices) {

		if(isset($uChoices['request1'])) {
			return true;
		}
		else if(isset($uChoices['request2'])) {
			//secondary choice selected, but no primary - error
			return false;
		}
		else if(count($uChoices) == 0) {
			return true;
		}
		else {
			//there are choices with unexpected type
			$this->_logger->log('unexpected type of choice',
				'Notice', Null, json_encode(array(
					'choices' => var_export($uChoices, true)
			)));
			return false;
		}
	}

	/**
	 * Checks if a class was selected multiple times
	 * @param  array  $choices All choices
	 * @return bool            True on no error, else false
	 */
	private function multipleSelectionsOfSameClassCheck($choices) {

		$selClasses = array();
		foreach($choices as $unitChoices) {
			foreach($unitChoices as $classId) {
				if(!in_array($classId, $selClasses, true)) {
					$selClasses[] = $classId;
				}
				else {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Checks if the user had already selected classes for the unit
	 * throws Exception on Error when checking
	 * @param  array  $choices The choices the user made
	 * @return bool            false if he did already chose a class at a unit,
	 *                         but a class he selected now was in this unit
	 *                         too. Else true
	 */
	private function noClassesAtUnitYetChosenCheck($choices) {

		$searchStr = 'c.ID IN(' . implode(', ', $this->_selClassIds) . ')';
		$userId = $this->_pdo->quote($_SESSION['uid']);

		try {
			$res = $this->_pdo->query(
				"SELECT COUNT(*) FROM KuwasysClasses c
					INNER JOIN jointUsersInClass uic ON uic.ClassID = c.ID
					INNER JOIN (
							SELECT DISTINCT unitId
								FROM KuwasysClasses c WHERE ({$searchStr})
						) ui
					WHERE ui.unitId = c.unitId AND uic.UserID = {$userId}
						AND c.schoolyearId = @activeSchoolyear"
			);
			$count = $res->fetchColumn();
			return (!(bool)$count);

		} catch (\PDOException $e) {
			$this->_logger->log('Error checking if classes where already ' .
				'chosen at that classUnit', 'Notice', Null,
				json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	/**
	 * Checks if registrations for the selected classes are allowed
	 * throws Exception on Error when checking
	 * @param  array  $choices The choices made by the user
	 * @return array           true if registrations allowed, else false
	 */
	private function classesChosenRegistrationAllowedCheck($choices) {

		$searchStr = 'c.ID IN(' . implode(', ', $this->_selClassIds) . ')';

		try {
			$res = $this->_pdo->query(
				"SELECT COUNT(*) FROM KuwasysClasses c
					WHERE ({$searchStr}) AND c.registrationEnabled = 0"
			);
			return ($res->fetchColumn() == 0);

		} catch (\PDOException $e) {
			$this->_logger->log(
				'Error checking for non-activated classes that were chosen ' .
				'by the user', 'Notice', Null,
				json_encode(array('msg' => $e->getMessage())));
			throw $e;
		}
	}

	/**
	 * Commits the choices the user has made
	 * Dies displaying a message on error
	 * @param  array  $choices The user's choices
	 */
	private function choicesUpload($choices) {

		try {
			$stmt = $this->_pdo->prepare(
				'INSERT INTO `jointUsersInClass` (UserID, ClassID, statusId)
					VALUES (?, ?, (
						SELECT ID FROM usersInClassStatus uics
							WHERE uics.name = ?
					))'
			);

			$this->_pdo->beginTransaction();
			foreach($choices as $unit) {
				foreach($unit as $statusName => $classId) {
					$stmt->execute(
						array($_SESSION['uid'], $classId, $statusName)
					);
				}
			}
			$this->_pdo->commit();

		} catch (\PDOException $e) {
			$this->_logger->log('Error uploading the user-choices',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieError(_g('Could not commit your choices!'));
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_selClassIds;
}

?>