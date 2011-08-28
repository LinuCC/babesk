<?php

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
    
    //the modules for the admin groups 
    require "../administrator/modules.php";
    
	//check if all elements exist
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		if (!isset($_POST['Host'], $_POST['Username'], $_POST['Password'], $_POST['Database'])) {
			die(INVALID_FORM);
		} //save values and check for empty fields
		if (($host = trim($_POST['Host'])) == '' OR
            ($username = trim($_POST['Username'])) == '' OR
            ($password = trim($_POST['Password'])) == '' OR
            ($database = trim($_POST['Database'])) == '') {
        		die(EMPTY_FORM);
   		}

		//try to connect to database, oppress warnings
		$db = @new mysqli($host, $username, $password, $database);
		if (mysqli_connect_errno()) {
			die('Verbindung mit der Datenbank konnte nicht hergestellt werden, bitte Verbindungsdaten erneut eingeben');
		}

		//create database.php file with supplied data
		$outFile = '../include/dbconnect.php';
		$fh = @fopen($outFile, 'w') or die('Datei konnte nicht gespeichert werden, bitte Benutzerrechte Ã¼berprÃ¼fen');
		$stringData = "<?php
    require_once 'constants.php';
    
    \$host = '".$host."';
    \$username = '".$username."';
    \$password = '".$password."';
    \$database = '".$database."';
    
    \$db = @new MySQLi(\$host, \$username, \$password, \$database);
    
    if (mysqli_connect_errno()) {
        exit(DB_CONNECT_ERROR.mysqli_connect_error());
    }

?>";

		fwrite($fh, $stringData);
		fclose($fh);

		$sql = array();


	    // ========== Create Tables ========== \\
		//Table 'meals'			//delete "if not exists"
		$sql[0] = 'CREATE TABLE IF NOT EXISTS `meals` (
                        `ID` int(11) unsigned NOT NULL auto_increment,
                        `name` varchar(255) NOT NULL,
                        `description` text NOT NULL,
                        `price_class` smallint(6) NOT NULL,
                        `date` date NOT NULL,
                        `max_orders` int(11) NOT NULL,
                        PRIMARY KEY  (`ID`)
                    ) AUTO_INCREMENT=1 ;';
		//Table 'users'
		$sql[1] = 'CREATE TABLE IF NOT EXISTS `users` (
                        `ID` bigint(20) unsigned NOT NULL auto_increment,
                        `name` varchar(255) NOT NULL,
                        `forename` varchar(255) NOT NULL,
                        `username` varchar(255) NOT NULL,
                        `password` varchar(32) NOT NULL,
                        `birthday` date NOT NULL,
                        `credit` decimal(4,2) NOT NULL,
                        `GID` smallint(5) unsigned NOT NULL,
                        `last_login` timestamp NOT NULL,
                        `login_tries` smallint(5),
                        `first_passwd` boolean NOT NULL,
                        PRIMARY KEY  (`ID`)
                    );';
		//Table 'price_classes'
		$sql[2] = 'CREATE TABLE IF NOT EXISTS `price_classes` (
                        `ID` smallint(5) unsigned NOT NULL,
                        `name` varchar(255) NOT NULL,
                        `GID` smallint(5) NOT NULL,
                        `price` decimal(4,2) NOT NULL
                    );';
		//Table 'groups'
		$sql[3] = 'CREATE TABLE IF NOT EXISTS `groups` (
                        `ID` smallint(5) unsigned NOT NULL auto_increment,
                        `name` varchar(255) NOT NULL,
                        `max_credit` decimal(4,2) NOT NULL,
                        PRIMARY KEY  (`ID`)
                    ) AUTO_INCREMENT=1;';
	    //Table 'orders'
		$sql[4] = 'CREATE TABLE IF NOT EXISTS `orders` (
		                `ID` int(11) unsigned NOT NULL auto_increment,
                        `MID` int(11) unsigned NOT NULL,
                        `UID` bigint(11) unsigned NOT NULL,
                        `date` date NOT NULL,
                        `IP` binary(16) NOT NULL,
                        `ordertime` timestamp NOT NULL,
                        `fetched` boolean NOT NULL
                    )';
        //Table 'cards'
		$sql[5] = 'CREATE TABLE IF NOT EXISTS `cards` (                       <a href="../administrator/modules/mod_fill/fill.php"></a>
                        `ID` bigint(20) unsigned NOT NULL auto_increment,
                        `cardnumber` varchar(10) NOT NULL,
                        `UID` bigint(11) unsigned NOT NULL,
                        PRIMARY KEY  (`ID`)
                    )AUTO_INCREMENT=1;';
        //Table 'logs'
		$sql[6] = 'CREATE TABLE IF NOT EXISTS `logs` (
                        `ID` bigint(20) unsigned NOT NULL auto_increment,
                        `category` smallint(5) NOT NULL,
                        `severity` smallint(5) NOT NULL,
                        `time` timestamp NOT NULL,
                        `message` varchar(255) NOT NULL,
                        PRIMARY KEY  (`ID`)
                    )AUTO_INCREMENT=1;';
        //Table 'administrators'
		$sql[7] = 'CREATE TABLE IF NOT EXISTS `administrators` (
                        `ID` smallint(5) unsigned NOT NULL auto_increment,
                        `name` varchar(255) NOT NULL,
                        `password` varchar(32) NOT NULL,
                        `GID` smallint(5) NOT NULL,
                        PRIMARY KEY  (`ID`)
                    )AUTO_INCREMENT=1;';
        //Table 'admin_groups'
        $sql[8] = 'CREATE TABLE IF NOT EXISTS `admin_groups` (
                        `ID` smallint(5) unsigned NOT NULL auto_increment,
                        `name` varchar(255) NOT NULL,
                        `modules` varchar(1024) NOT NULL,
                        PRIMARY KEY  (`ID`)
                    )AUTO_INCREMENT=1;';
        //Table 'IP'
        $sql[9] = 'CREATE TABLE IF NOT EXISTS `ip` (
                        `IP` varchar(20) NOT NULL,
                        `time` timestamp NOT NULL,
                        `login_tries` smallint(2) NOT NULL,
                        PRIMARY KEY (`IP`)
                    );';

        // ========== Execute Queries ========== \\
		foreach ($sql as $query) {
	    	$result = $db->query($query);
			if (!$result) {
	    		die (DB_QUERY_ERROR.$db->error."<br \>Bitte Datenbank leeren");
			}
		}
		

		//next step
		require "generals.tpl";

	}
	else {
       require "database.tpl";
    }



?>