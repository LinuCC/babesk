<?php

    //no direct access
    defined('_AEXEC') or die("Access denied");

	require_once PATH_SITE."/include/logs.php";
	
	global $smarty;
	
	//the different actions the module can do
	$_showLogs = 'show';
	$_delLogs  = 'delete';

    if(isset($_GET['action'])) {
        if($_GET['action'] == $_showLogs) {
            if ('POST' == $_SERVER['REQUEST_METHOD']) {
        	    // Überprüfung des POST
        	    if (!isset($_POST['Category'], $_POST['Severity'])) {
        		   die(INVALID_FORM);
        	    }
        	    $category = trim($_POST['Category']);
                $severity = trim($_POST['Severity']);

                Logger::printLogs($category, $severity);                      
        
            }
            else {
	           //Einbinden des Templates
                $smarty->display('administrator/modules/mod_logs/showLogs.tpl');
            }
        }
        if($_GET['action'] == $_delLogs) {
            Logger::clearLogs();        
        }
    }
	else {
	    //Einbinden des Templates
        $smarty->display('administrator/modules/mod_logs/logs.tpl');
    }

?>