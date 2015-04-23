<?php

namespace administrator\Schbas\SchbasAccounting;

require_once PATH_INCLUDE . '/Module.php';
require_once 'SchbasAccounting.php';

class RecordReceipt extends \SchbasAccounting {

	///////////////////////////////////////////////////////////////////////
	//Constructor
	///////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
	}

	///////////////////////////////////////////////////////////////////////
	//Methods
	///////////////////////////////////////////////////////////////////////

	/**
	 * Moduleexecution starts here
	 */
	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);

		try {
			$count = $this->_em->createQuery(
				'SELECT COUNT(a) FROM DM:SchbasAccounting a'
			)->getSingleScalarResult();
			if(!$count) {
				$this->_interface->dieMsg('Es gibt keine Einträge.');
			}
		}
		catch(\Exception $e) {
			$this->_logger->logO('Could not check if accounting-entries exist',
				['sev' => 'error', 'more' => $e->getMessage()]);
			$this->_interface->dieError('Konnte die Einträge nicht checken');
		}


		if(isset($_POST['filter'])) {
			$this->userdataAjaxSend();
		}
		else if(isset($_POST['userId'], $_POST['amount'])) {
			$this->paidAmountChange($_POST['userId'], $_POST['amount']);
		}
		else {
			$this->displayTpl('record-receipt.tpl');
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Implements
	///////////////////////////////////////////////////////////////////////

	/**
	 * Initializes various variables to use in the module
	 */
	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->initSmartyVariables();
	}

	/**
	 * Sends the client the data of the users he requested as json
	 * Dies sending json
	 */
	private function userdataAjaxSend() {

		$showOnlyMissing = ($_POST['showOnlyMissing'] == 'true')
			? true : false;
		$data = $this->userdataFetch(
			$_POST['filter'],
			$_POST['filterForColumns'],
			$_POST['sortColumn'],
			$_POST['activePage'],
			$showOnlyMissing
		);
		if(count($data['users'])) {
			die(json_encode(array(
				'users' => $data['users'], 'pagecount' => $data['pagecount']
			)));
		}
		else {
			http_response_code(204);
			die(json_encode(array(
				'message' => 'Keine Benutzer gefunden!'
			)));
		}
	}

	/**
	 * Fetches the userdata by the given parameter
	 * @param  string $filter  Filters the users. Can be void
	 * @param  string $pagenum The number of page requested
	 * @return array           [
	 *                             'users' => [<userdata>],
	 *                             'pagecount' => <pagecount>
	 *                         ]
	 */
	private function userdataFetch(
		$filter, $filterForCol, $sortColumn, $pagenum, $showOnlyMissing
	) {

		$query = $this->userdataQueryCreate(
			$filter, $filterForCol, $sortColumn, $pagenum, $showOnlyMissing
		);
		$paginator = new \Doctrine\ORM\Tools\Pagination\Paginator(
			$query, $fetchJoinCollection = true
		);
		$users = array();
		if(count($paginator)) {
			foreach($paginator as $page) {
				$user = $page[0];
				//Doctrines array-hydration treats foreign keys different
				$user['cardnumber'] = $page['cardnumber'];
				$user['payedAmount'] = (isset($page['payedAmount'])) ?
					$page['payedAmount'] : 0.00;
				$user['amountToPay'] = (isset($page['amountToPay'])) ?
					$page['amountToPay'] : 0.00;
				$user['missingAmount'] = (isset($page['missingAmount'])) ?
					$page['missingAmount'] : 0.00;
				$user['loanChoice'] = $page['loanChoice'];
				$user['loanChoiceAbbreviation'] =
					$page['loanChoiceAbbreviation'];
				$user['activeGrade'] = $page['activeGrade'];
				$users[] = $user;
			}
			$pagecount = ceil((int) count($paginator) / $this->_usersPerPage);
			$pagecount = ($pagecount != 0) ? $pagecount : 1;
			return array('users' => $users, 'pagecount' => $pagecount);
		}
		else {
			return array('users' => array(), 'pagecount' => 1);
		}
	}

	/**
	 * Creates the Query with which to fetch the userdata
	 * @param  string $filter  Filters the users. Can be void
	 * @param  string $pagenum The number of page requested
	 * @return Query           A doctrine query object for fetching the users
	 */
	private function userdataQueryCreate(
		$filter, $filterForCol, $sortColumn, $pagenum, $showOnlyMissing
	) {

		$queryBuilder = $this->_em->createQueryBuilder()
			->select(
				'partial u.{id, forename, name, username}, c.cardnumber, ' .
				'a.payedAmount, a.amountToPay, lc.name AS loanChoice, ' .
				'lc.abbreviation AS loanChoiceAbbreviation, ' .
				'a.amountToPay - a.payedAmount AS missingAmount, ' .
				'CONCAT(g.gradelevel, g.label) AS activeGrade'
			)->from('DM:SystemUsers', 'u')
			->leftJoin('u.schbasAccounting', 'a')
			->leftJoin('u.cards', 'c')
			->leftJoin('u.attendances', 'uigs')
			->leftJoin('uigs.schoolyear', 's', 'WITH', ' s.active = 1')
			->leftJoin('uigs.grade', 'g')
			->leftJoin('a.loanChoice', 'lc')
			->andWhere('uigs.grade IS NULL OR s.id IS NOT NULL');
		if($showOnlyMissing) {
			$queryBuilder->having('missingAmount > 0');
		}
		if(!empty($filter) && !empty($filterForCol)) {
			$filters = array();
			if(in_array('cardnumber', $filterForCol)) {
				$filters[] = 'c.cardnumber LIKE :filter';
			}
			if(in_array('grade', $filterForCol)) {
				$filters[] = 'CONCAT(g.gradelevel, g.label) LIKE :filter';
			}
			if(in_array('username', $filterForCol)) {
				$filters[] = 'u.username LIKE :filter';
			}
			$str = implode(' OR ', $filters);
			$queryBuilder->andWhere($str);
			$queryBuilder->setParameter('filter', "%${filter}%");
		}
		$this->_logger->log($sortColumn);
		if(!empty($sortColumn)) {
			if($sortColumn == 'grade') {
				$queryBuilder->orderBy('activeGrade');
			}
			else if($sortColumn == 'name') {
				$queryBuilder->orderBy('u.name');
			}
			else {
				$this->_logger->log('Unknown column to sort for',
					'Notice', Null, json_encode(array('col' => $sortColumn)));
			}
		}
		$queryBuilder->setFirstResult(($pagenum - 1) * $this->_usersPerPage)
			->setMaxResults($this->_usersPerPage);
		$query = $queryBuilder->getQuery();
		//For performance, paginator eats arrays, too
		$query->setHydrationMode(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
		return $query;
	}

	private function paidAmountChange($userId, $amount) {

		try {
			require_once PATH_INCLUDE . '/Schbas/Loan.php';
			$loanHelper = new \Babesk\Schbas\Loan($this->_dataContainer);
			$user = $this->_em->getRepository('DM:SystemUsers')
				->findOneById($userId);
			$schoolyear = $loanHelper->schbasPreparationSchoolyearGet();
			if(!isset($user)) {
				throw new Exception('User not found!');
			}
			$accounting = $this->_em->getRepository('DM:SchbasAccounting')
				->findOneBy(['user' => $user, 'schoolyear' => $schoolyear]);
			$accounting->setPayedAmount($amount);
			$paid = $accounting->getPayedAmount();
			$toPay = $accounting->getAmountToPay();
			$missing = $toPay - $paid;
			$this->_em->persist($accounting);
			$this->_em->flush();
			die(json_encode(array(
				'userId' => $userId, 'paid' => $paid, 'missing' => $missing
			)));

		} catch(Exception $e) {
			$this->_logger->log('Error updating the paid amount of an user',
				'error', Null, json_encode(array('uid' => $userId,
					'amount' => $amount, 'msg' => $e->getMessage())));
			http_response_code(500);
		}
	}

	///////////////////////////////////////////////////////////////////////
	//Attributes
	///////////////////////////////////////////////////////////////////////

	private $_usersPerPage = 10;
}

?>