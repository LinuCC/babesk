<?php

    //no direct access
    defined('_AEXEC') or die("Access denied");

	global $smarty;

	if ('POST' == $_SERVER['REQUEST_METHOD']) {
	   // Überprüfung des POST
	   if (!isset($_POST['name'])) {
		  die(INVALID_FORM);
		}  //save values and check for empty fields
		if (($name = trim($_POST['name'])) == '') {
	        die(EMPTY_FORM);
	   	}

        //Auswertung des POST
        $smarty->assign('name', $name);
        $smarty->display('administrator/modules/mod_dummy/dummy.tpl');             

    }
	else {
	    //Einbinden des Templates
        $smarty->display('administrator/modules/mod_dummy/form.tpl');
    }

?>