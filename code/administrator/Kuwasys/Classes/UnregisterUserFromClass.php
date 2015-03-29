<?php

namespace administrator\Kuwasys\Classes;

require_once 'Classes.php';

/**
 * Allows unregistering a user from a class
 * Replies with ajax-data
 */
class UnregisterUserFromClass extends \Classes {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_POST['joinId']) && !isBlank($_POST['joinId'])) {
			if($this->joinDelete($_POST['joinId'])) {
				$this->_interface->dieAjax(
					'success',
					'Der Benutzer wurde erfolgreich vom Kurs abgemeldet.'
				);
			}
			else {
				$this->_interface->dieAjax(
					'error', 'Konnte den Benutzer nicht vom Kurs abmelden.'
				);
			}
		}
		else {
			$this->_interface->dieAjax('error', 'Keine join-Id gegeben');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
	}

	/**
	 * Deletes the join that links the user to the class
	 * @return bool   If the link got deleted or not
	 */
	protected function joinDelete($id) {

		try {
			$stmt = $this->_pdo->prepare(
				'DELETE FROM KuwasysUsersInClassesAndCategories WHERE ID = ?'
			);
			$stmt->execute(array($id));
			return (boolean) $stmt->rowCount();

		} catch (\PDOException $e) {
			$this->_logger->log('error unregistering user from class',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieAjax(
				'error',
				'Ein Fehler ist beim abmelden des Nutzers aufgetreten'
			);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>