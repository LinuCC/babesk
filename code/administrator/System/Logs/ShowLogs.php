<?php

namespace administrator\System\Logs;

require_once 'Logs.php';

class ShowLogs extends \administrator\System\Logs {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		if(isset($_POST['getData'])) {
			$data = array(
				'logs' => $this->logsFetch(),
				'count' => $this->logsCountFetch()
			);
			if(isset($_POST['fetchCategories']) &&
				$_POST['fetchCategories'] == 'true') {
				$data['categories'] = $this->categoriesFetch();
			}
			if(isset($_POST['fetchSeverities']) &&
				$_POST['fetchSeverities'] == 'true') {
				$data['severities'] = $this->severitiesFetch();
			}
			$data['tableSize'] = $this->sizeFetch();
			$this->_interface->dieAjax('success', $data);
		}
		else {
			$this->displayTpl('showlogs.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->moduleTemplatePathSet();
	}

	protected function logsFetch() {

		//Create query-string
		$filterStr = (!empty($_POST['filter']))
			? ' WHERE l.message LIKE :filter OR
				l.additionalData LIKE :filter ' : '';
		$filterCatStr = (!empty($_POST['category'])) ?
			'AND lc.ID = :categoryId' : '';
		$filterSevStr = (!empty($_POST['severity'])) ?
			'AND ls.ID = :severityId' : '';
		$query = "SELECT l.*, lc.name AS categoryName, ls.name AS severityName
					FROM SystemLogs l
					INNER JOIN SystemLogCategories lc ON lc.ID = l.categoryId
						$filterCatStr
					INNER JOIN SystemLogSeverities ls ON ls.ID = l.severityId
						$filterSevStr
					{$filterStr}
					ORDER BY l.date DESC
					LIMIT :startLog, :logsPerPage";
		try {
			$stmt = $this->_pdo->prepare($query);

			//Insert parameters
			$_POST['logsPerPage'] = (int) $_POST['logsPerPage'];
			$stmt->bindParam(
				'logsPerPage', $_POST['logsPerPage'], \PDO::PARAM_INT
			);
			$startLog = ($_POST['activePage'] - 1) * $_POST['logsPerPage'];
			$stmt->bindParam('startLog', $startLog, \PDO::PARAM_INT);
			if(!empty($_POST['filter'])) {
				$filter = '%' . $_POST['filter'] . '%';
				$stmt->bindParam('filter', $filter);
			}
			if(!empty($_POST['category'])) {
				$stmt->bindParam('categoryId', $_POST['category']);
			}
			if(!empty($_POST['severity'])) {
				$stmt->bindParam('severityId', $_POST['severity']);
			}

			//Execute query
			$stmt->execute();
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);

		} catch (\PDOException $e) {
			$this->_logger->log('error fetching the logs',
				'Moderate', Null, json_encode(array(
					'msg' => $e->getMessage(), 'query' => $query)));
			$this->_interface->dieAjax(
				'error', 'Konnte die Logs nicht abrufen'
			);
		}
	}

	protected function logsCountFetch() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT COUNT(*) FROM SystemLogs'
			);
			$stmt->execute();
			return $stmt->fetchColumn();

		} catch (\PDOException $e) {
			$this->_logger->log('error counting the logs',
				'Moderate', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieAjax(
				'error', 'Konnte die Logs nicht zählen'
			);
		}
	}

	protected function categoriesFetch() {

		try {
			$res = $this->_pdo->query(
				'SELECT ID, name FROM SystemLogCategories'
			);
			return $res->fetchAll(\PDO::FETCH_KEY_PAIR);

		} catch (\PDOException $e) {
			$this->_logger->log('error fetching the categories of logs',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieAjax(
				'error', 'Konnte die Kategorien nicht abrufen'
			);
		}
	}

	protected function severitiesFetch() {

		try {
			$res = $this->_pdo->query(
				'SELECT ID, name FROM SystemLogSeverities'
			);
			return $res->fetchAll(\PDO::FETCH_KEY_PAIR);

		} catch (\PDOException $e) {
			$this->_logger->log('error fetching the severities of logs',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieAjax(
				'error', 'Konnte die Gewichtungen nicht abrufen'
			);
		}
	}

	/**
	 * Fetches the size of the logs-table
	 * @return string The size of the logs-table in Megabyte, or false if an
	 *                error occured
	 */
	protected function sizeFetch() {

		try {
			$res = $this->_pdo->query(
				'SELECT round(((data_length + index_length) / 1024 / 1024), 2)
				FROM information_schema.TABLES
				WHERE table_schema = DATABASE()
					AND table_name = "SystemLogs";
			');
			return $res->fetchColumn();

		} catch (\PDOException $e) {
			$this->_logger->log('Error fetching the size of the logs-table',
				'Notice', Null, json_encode(array('msg' => $e->getMessage())));
			return false;
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>