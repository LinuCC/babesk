<?php

/**
 * @todo: Remove this file and replace the occurences with DBConnect.php and the Class DBConnect
 */

//dbconnect gets included from various sources, thus making include-Paths different. Fix with __DIR__
require_once __DIR__ . '/DBConnect.php';

$host = 'localhost';
$username = 'root';
$password = '';;
$database = 'babesk';

// $dbObject = new DBConnect($host, $username, $password, $database);
$dbObject = new DBConnect();
$dbObject->initDatabaseFromXML();
$db = $dbObject->getDatabase();

?>