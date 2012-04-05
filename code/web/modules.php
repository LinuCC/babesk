<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");

    $modules = array();

    //---------------------------------
    //      Available Modules
    //---------------------------------
    $modules[] = "menu";
    $modules[] = "help";
    $modules[] = "account";
    $modules[] = "order";
    $modules[] = "cancel";
    $modules[] = "stars";
    $modules[] = "comment";
	$modules[] = "logout";
	$modules[] = 'change_password';

    /*if (isset($_GET['section'], $sites[$_GET['section']])) {
        if (file_exists("modules/".$sites[$_GET['section']])) {
            require "modules/".$sites[$_GET['section']];
        } else {
            echo "Datei nicht vorhanden";
        }
    } else {
		//Wenn keine Seite bzw. eine nicht vorhandene angegeben ist und man eingeloggt ist immer Startanzeige laden
        require "modules/".$sites['start'];
    }*/
?>