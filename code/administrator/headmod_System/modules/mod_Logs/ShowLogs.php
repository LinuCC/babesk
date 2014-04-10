<?php

namespace administrator\System\Logs;

require_once 'Logs.php';

class ShowLogs extends \administrator\System\Logs {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		if(isset($_GET['data'])) {
			$this->_interface->dieAjax('success', $this->logsFetch());
		}
		else {
			$this->displayTpl('showlogs.tpl');
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	public function logsFetch() {

		try {
			$stmt = $this->_pdo->prepare(
				'SELECT * FROM SystemLogs'
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