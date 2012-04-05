<?php
/*******************************************************************************
* Installations Skript - Richtet die Datenbank ein                             *
*******************************************************************************/

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	ini_set("default_charset", "utf-8");
	//if this value is not set, the modules will not execute
    define('_AEXEC', 1);

    //check if setup was previously done
    /*if(file_exists("../include/dbconnect.php")) {
        include '../include/dbconnect.php';
        $sql = 'SHOW TABLES';
        $result = $db->query($query);
		$row = $result->fetch_assoc();
		var_dump($row);    
    }*/ 

    include 'header.tpl';

    require_once '../include/constants.php';		
	require_once '../include/functions.php';
	require_once "../include/path.php";
	
	require '../smarty/smarty_init.php';
	
	$smarty->assign('smarty_path', REL_PATH_SMARTY);
    $smarty->assign('status', '');
		
	if(isset($_GET['step'])) {
                                     
        //------------------------------------------
    	// ======= Store Database Login Data and Setup Database (Step 1) =======
    	//------------------------------------------
    		
    	if($_GET['step'] == '1') {
    	   require "database.php";
    	}
    	   
    	//----------------------------------------------------------------------
    	// ======= Get General Data (Step 2) =======
    	//----------------------------------------------------------------------
    						   
        if($_GET['step'] == '2') {
            require "generals.php";
        }
    				
    	//------------------------------------------
    	// ======= Setup the Groups (Step 3) =======
    	//------------------------------------------
    	
    	if($_GET['step'] == '3') {
            require "groups.php";
        }
    	   
    	   
    	//-------------------------------------------------
    	// ======= Setup the Price Classes (Step 4) =======
    	//-------------------------------------------------
    	
    	if($_GET['step'] == '4') {
    		require "price_classes.php";
    	}
    	
    	
    	//-------------------------------------------------
    	// ======= Finish (Step 5) =======
    	//-------------------------------------------------
    	
    	if($_GET['step'] == '5') {
            include "finish.tpl"; 
        }
    }
    else {
        require "database.tpl";
    }
	
?>
	
</body>
</html>


