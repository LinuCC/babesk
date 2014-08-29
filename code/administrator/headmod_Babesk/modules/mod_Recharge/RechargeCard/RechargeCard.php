<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_ADMIN . '/headmod_Babesk/modules/mod_Recharge/Recharge.php';

class RechargeCard extends Recharge {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_POST['filter'])) {
			$this->userdataAjaxSend();
		}
		else if(isset($_POST['userId'], $_POST['credits'])) {
			$this->creditsChange($_POST['userId'], $_POST['credits']);
		}
		else {
			$this->displayTpl('userlist.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		parent::initSmartyVariables();
	}

	private function userdataAjaxSend() {

		$this->userdataFetch($_POST['filter'], $_POST['activePage']);
		die(json_encode('yay!'));
	}

	private function userdataFetch($filter, $pagenum) {

		$query = $this->userdataQueryCreate($filter, $pagenum);
		$paginator = new \Doctrine\ORM\Tools\Pagination\Paginator(
			$query, $fetchJoinCollection = true
		);
		$users = array();
		if(count($paginator)) {
			foreach($paginator as $page) {
				$user = $page[0];
				//Doctrines array-hydration treats foreign keys different
				$user['cardnumber'] = $page['cardnumber'];
				$users[] = $user;
			}
			$pagecount = ceil((int) count($paginator) / $this->_usersPerPage);
			$pagecount = ($pagecount != 0) ? $pagecount : 1;
			die(json_encode(array(
				'users' => $users,
				'pagecount' => $pagecount
			)));
		}
		else {
			http_response_code(204);
			die(json_encode(array(
				'message' => 'Keine Einträge gefunden!'
			)));
		}
	}

	private function userdataQueryCreate($filter, $pagenum) {

		//"partial": different notation because the Paginator cant use the
		//standard u.id, u.name...
		$queryBuilder = $this->_entityManager->createQueryBuilder()
			->select(
				'partial u.{id, forename, name, username, credit}, ' .
				'c.cardnumber'
			)
			->from('Babesk:SystemUsers', 'u')
			->leftJoin('u.cards', 'c');
		if(!empty($filter)) {
			$queryBuilder->where(
					'u.username LIKE :filter OR c.cardnumber LIKE :filter'
				)->setParameter('filter', "%${filter}%");
		}
		$queryBuilder->setFirstResult(($pagenum - 1) * $this->_usersPerPage)
			->setMaxResults($this->_usersPerPage);

		$query = $queryBuilder->getQuery();
		$query->setHydrationMode(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
		return $query;
	}

	private function creditsChange($userId, $credits) {

		try {
			$user = $this->_entityManager->getRepository('Babesk:SystemUsers')
				->findOneById($userId);
			if(!isset($user)) {
				throw new Exception('User not found!');
			}
			$maxCredits = $user->getPriceGroup()->getMaxCredit();
			if($credits <= $maxCredits) {
				$user->setCredit($credits);
				$this->_entityManager->persist($user);
				$this->_entityManager->flush();
				die(json_encode(array(
					'userId' => $userId, 'credits' => $credits
				)));
			}
			else {
				http_response_code(500);
				die(json_encode(array(
					'message' => "Die eingegebenen ${credits} € " .
						"übersteigen den maximalen Wert der Preisgruppe des " .
						"Nutzers von ${maxCredits} €."
				)));
			}

		} catch (Exception $e) {
			$this->_logger->log('Error updating the credits of an user',
				'Moderate', Null, json_encode(array('uid' => $userId,
					'credits' => $credits, 'msg' => $e->getMessage())));
			http_response_code(500);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_usersPerPage = 10;
}

?>