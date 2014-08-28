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

		$this->userdataFetch($_POST['filter'], 1);
		die(json_encode('yay!'));
	}

	private function userdataFetch($filter, $pagenum) {

		$query = $this->userdataQueryCreate($filter, $pagenum);
		$paginator = new \Doctrine\ORM\Tools\Pagination\Paginator(
			$query, $fetchJoinCollection = true
		);
		var_dump(count($paginator));
		die(json_encode(array(
			'users' => $paginator
		)));
	}

	private function userdataQueryCreate($filter, $pagenum) {

		$queryBuilder = $this->_entityManager->createQueryBuilder()
			->select('u.forename, u.name, u.username, u.cardnumber')
			->from('Babesk:SystemUsers', 'u');
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