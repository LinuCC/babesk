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
			$this->_interface->dieAjax(
				'success', array('logs' => $this->logsFetch())
			);
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

	public function logsFetch() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT * FROM SystemLogs LIMIT 0, :logsPerPage'
			);
			$_POST['logsPerPage'] = (int) $_POST['logsPerPage'];
			$stmt->bindParam(
				'logsPerPage', $_POST['logsPerPage'], \PDO::PARAM_INT
			);
			$stmt->execute();
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);

		} catch (\PDOException $e) {
			$this->_logger->log('error fetching the logs',
				'Moderate', Null, json_encode(array('msg' => $e->getMessage())));
			$this->_interface->dieAjax(
				'error', 'Konnte die Logs nicht abrufen'
			);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}

?>