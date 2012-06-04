<?php

require_once '../path.php';
require_once PATH_CODE . 'include/sql_access/DBConnect.php';

class DBConnectTest extends PHPUnit_Framework_TestCase {
	
	protected $_dbConnect;
	
	protected function setUp() {
		$this->_dbConnect = new DBConnect();
	}
	
	public function testCreateDatabase() {
		
	}
	
	public function providerCreateDatabase() {
		
	}
	
	/**
	 * @dataProvider providerValidDatabaseXML
	 */
	public function testValidDatabaseXML ($dbConnect, $returnValue) {
		
		$this->assertEquals($returnValue, $dbConnect->validateDatabaseXML());
	}
	
	public function providerValidDatabaseXML() {
		
		$dbClassArr = array();
		
		$dbClassArr [1] = new DBConnect();
		$dbClassArr [1] -> setDatabaseXMLPath(__DIR__ . '/testXML/testDatabaseValues1.xml');
		$dbClassArr [2] = new DBConnect();
		$dbClassArr [2] -> setDatabaseXMLPath(__DIR__ . '/testXML/testDatabaseValues2.xml');
		$dbClassArr [3] = new DBConnect();
		$dbClassArr [3] -> setDatabaseXMLPath('Definetly a wrong path');
		
		return array(
				array($dbClassArr [1], true),
				array($dbClassArr [2], false),
				array($dbClassArr [3], false),
				);
	}
	
// 	public function testDatabaseAlive() {
// 	//	$dbConnect = new DBConnect($host, $username, $password, $databaseName);
// 	}
	
}

?>