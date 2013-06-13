<?php

require_once PATH_INCLUDE . '/CsvImport.php';

class UserCsvImport extends CsvImport {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		parent::__construct();
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Uploads the UserCsv-Data to the Server. finalize() takes care off
	 * checking wether the stuff uploaded as a Transaction should be stored
	 * permanently or thrown away.
	 */
	protected function upload() {

		$this->uploadStart();

		// $lulz = $db->query('SELECT @@autocommit');
		// $db = TableMng::getDb();
		// $db->autocommit(false);

		// $res = $db->multi_query(
		// 	'
		// 	START TRANSACTION;
		// 	INSERT INTO users
		// 		(forename, name)
		// 	VALUES ("moin", "noob");
		// 	ROLLBACK;
		// 	');


		// $db->rollback();

		// $db->autocommit(true);


		// echo 'schinken';
		// var_dump(TableMng::getDb()->rollback());


		// var_dump($lulz);
		// var_dump(TableMng::getDb()->rollback());

		/**
		 * @fix: transactions not working. fuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuuu
		 */

		require_once PATH_ACCESS . '/DBConnect.php';

		$dbObject = new DBConnect();
		$dbObject->initDatabaseFromXML();
		$db = $dbObject->getDatabase();
		$db->query('set names "utf8";');

		// $db->autocommit(false);

		$stmt = $db->prepare(sprintf(
			'INSERT INTO users (forename, name) VALUES (?, ?);
			'));

		foreach($this->_contentArray as $con) {
			$stmt->bind_param('ss', $con['forename'], $con['name']);
			if($stmt->execute()) {
				$this->previewDataAdd($con);
			}
			else {
				$this->_errors['dbUpload'][] = $con;
			}
		}
		$stmt->close();

		// $db->rollback();

		// $db->autocommit(true);

		$this->uploadFinalize();
	}

	/**
	 * Adds some data to the Class's previewData-Arrays which get converted to
	 * a table
	 *
	 * Dont forget to update the _previewDataHead-Values when changing the
	 * SQL-Query
	 *
	 * @param  array $con The Content of a row
	 */
	protected function previewDataAdd($con) {

		//Preview only the first 25 Elements
		if(count($this->_previewData) <= 25) {
			if(empty($this->_previewDataHead)) {
				$this->_previewDataHead = array('forename', 'name');
			}

			$this->_previewData[] = array(
				'forename' => $con['forename'],
				'name' => $con['name']);
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////
}


?>