<?php

namespace administrator\System\Users\Search;

require_once PATH_ADMIN . '/System/Users/Users.php';

class Search extends \administrator\System\Users\Users {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$username = filter_input(INPUT_GET, 'username');
		$schoolyearId = filter_input(INPUT_GET, 'schoolyearId');
		if($username && $schoolyearId) {
			$schoolyear = $this->_em->getReference(
				'DM:SystemSchoolyears', $schoolyearId
			);
			dieJson($this->searchByUsernameAndSchoolyear(
				$username, $schoolyear, 20
			));
		}
		else {
			dieHttp('Such-parameter fehlt', 400);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function searchByUsernameAndSchoolyear(
		$username, $schoolyear, $entryCount
	) {

		try {
			$query = $this->_em->createQuery(
				'SELECT u FROM DM:SystemUsers u
				INNER JOIN u.attendances a WITH a.schoolyear = :schoolyear
				WHERE u.username LIKE :username
			');
			$query->setParameter('username', "%$username%");
			$query->setParameter('schoolyear', $schoolyear);
			$query->setMaxResults($entryCount);
			$users = $query->getResult();
			$userArray = [];
			if(count($users)) {
				foreach($users as $user) {
					$userArray[] = [
						'id' => $user->getId(),
						'username' => $user->getUsername()
					];
				}
			}
			return $userArray;
		}
		catch (\Exception $e) {
			$this->_logger->logO('Could not search the users by username ' .
				'and schoolyear', ['sev' => 'error', 'moreJson' =>
				['username' => $username, 'msg' => $e->getMessage()]]);
			dieHttp('Konnte nicht nach dem Benutzernamen suchen', 500);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}
?>