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
		foreach($paginator as $page) {
			$userdata = $page[0];
			$user = array(
				'forename' => $page[0]['forename'],
				'name' => $page[0]['name'],
				'username' => $page[0]['username'],
				//Doctrines array-hydration treats foreign keys different
				'cardnumber' => $page['cardnumber'],
			);
			$users[] = $user;
			$pagecount = ceil((int) count($paginator) / $this->_usersPerPage);
			$pagecount = ($pagecount != 0) ? $pagecount : 1;
		}
		die(json_encode(array(
			'users' => $users,
			'pagecount' => $pagecount
		)));
	}

	private function userdataQueryCreate($filter, $pagenum) {

		//"partial": different notation because the Paginator cant use the
		//standard u.id, u.name...
		$queryBuilder = $this->_entityManager->createQueryBuilder()
			->select('partial u.{id, forename, name, username}, c.cardnumber')
			->from('Babesk:SystemUsers', 'u')
			->leftJoin('u.cards', 'c');
		if(!empty($filter)) {
			$queryBuilder->where(
					'u.username LIKE :filter OR u.cardnumber LIKE :filter'
				)->setParameter('filter', "%${filter}%");
		}
		$queryBuilder->setFirstResult($pagenum * $this->_usersPerPage)
			->setMaxResults($this->_usersPerPage);

		$query = $queryBuilder->getQuery();
		$query->setHydrationMode(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
		return $query;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	private $_usersPerPage = 10;
}

?>