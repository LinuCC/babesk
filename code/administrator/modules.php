<?php
/**
 * Defines the modules available to the administrator frontend
 * 
 * All modules are saved in an array and are identified by their name. 
 * The ModuleManager reads from this array to determine the available modules.
 * This list is also used in the menu.php an other parts.
 * 
 * All new modules have to be named here to become part of the system!
 * In addition to that, you have to add an entry in locales.php for the modul to be actually shown
 * 
 */
    //No direct access
    defined('_AEXEC') or die("Access denied");
    
    
    $modules = array();
    
    //---------------------------------
    //      Available Modules
    //---------------------------------    
    $modules[] = "register";
    $modules[] = "recharge";
    $modules[] = "checkout";
    $modules[] = "logs";
    $modules[] = "meals";
    $modules[] = "admins";
    //$modules[] = "fill";
    //$modules[] = "clear";
    //$modules[] = "dummy";
    $modules[] = "menu";
    $modules[] = "groups";
    
    
?>