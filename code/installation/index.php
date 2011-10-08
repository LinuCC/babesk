<?php
/*******************************************************************************
* Installations Skript - Richtet die Datenbank ein                             *
*******************************************************************************/

    error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
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
    	   	if ('POST' == $_SERVER['REQUEST_METHOD'] AND !$_SESSION['processed_POST']) {
    			if (!isset($_POST['Name'], $_POST['Price'])) {
    				die(INVALID_FORM);
    			}  //save values and check for empty fields
    			if (($pc_name = trim($_POST['Name'])) == '') {
    	        	die(EMPTY_FORM);
    	   		}
    	   		foreach ($_POST['Price'] as $price) {
    	   			if (trim($price) == '') {
    	   				die(EMPTY_FORM);
    	   			}
    	   		} 
    	   		//TODO check for correct form
    	
    			$gid = 1;
                foreach ($_POST['Price'] as $price) { 
    	         	addPriceClass($pc_name, $gid, $price); 
    			  	$gid++;
    			}
    		}
    			
    		include "step4.tpl";
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


