<?php

require_once PATH_STATISTICS_CHART . '/StatisticsBarChart.php';

class MessageStatSavedCopiesByTeacher extends StatisticsBarChart {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function imageDraw($externalData = NULL) {

		$this->_heading['text'] = 'Eingespartes Papier';
		$this->setMarginRatio(array('X' => 0.15, 'Y' => 0.15));
		$this->setScale(array("GridR" => 200, "GridG" => 200,"GridB" => 200,
			"DrawSubTicks" => TRUE, 'LabelRotation'  =>  45,
			"Pos" => SCALE_POS_TOPBOTTOM, "Mode" => SCALE_MODE_ADDALL_START0));
		parent::imageDraw();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function dataFetch() {

		$this->_savedCopiesData = TableMng::query(
			"SELECT u.forename, u.name, m.authorId, m.savedCopies FROM MessageCarbonFootprint AS m, users as u WHERE u.ID=m.authorId ORDER BY m.savedCopies DESC");
	}

	protected function dataProcess() {

		$authorIds = array();
		$copiesSaved = array();
		$this->_pData = new pData();

 		foreach($this->_savedCopiesData as $savedCopies) {
 		
 			$authorIds[] = $savedCopies['forename']." ".$savedCopies['name'];
 			$copiesSaved[] = $savedCopies['savedCopies']; 	

 		}
		
 		$this->_pData->addPoints($copiesSaved, 'Blatt DIN A4');
 		$this->_pData->addPoints($authorIds, 'Autor-ID');
 		$this->_pData->setAbscissa('Autor-ID');
 			
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_savedCopiesData;
}

?>
