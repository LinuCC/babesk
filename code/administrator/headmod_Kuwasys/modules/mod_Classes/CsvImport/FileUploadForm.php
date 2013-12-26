<?php

namespace administrator\Kuwasys\Classes\CsvImport;

require_once 'CsvImport.php';

class FileUploadForm extends \administrator\Kuwasys\Classes\CsvImport {

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		parent::entryPoint($dataContainer);
		$this->displayTpl('fileUploadForm.tpl');
	}
}


?>