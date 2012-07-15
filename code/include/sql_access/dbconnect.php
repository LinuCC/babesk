<?php

/**
 * @todo: Remove this file and replace the occurences with DBConnect.php and the Class DBConnect
 */

require_once dirname(__FILE__) . '/DBConnect.php.bak';

$host = 'localhost';
$username = 'root';
$password = '';;
$database = 'babesk';

// $dbObject = new DBConnect($host, $username, $password, $database);
$dbObject = new DBConnect();
$dbObject->initDatabaseFromXML();
$db = $dbObject->getDatabase();

?>