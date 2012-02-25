<?php
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 0);
    
    session_name('sid');
    session_start();
    ini_set("default_charset", "utf-8");
    
    //if this value is not set, the modules will not execute
    define('_AEXEC', 1);
    
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once "../include/path.php";
	require_once PATH_SMARTY."/smarty_init.php";
    require_once PATH_INCLUDE."/logs.php";
    require_once PATH_INCLUDE."/functions.php";
    require_once PATH_INCLUDE.'/exception_def.php';
    require_once PATH_INCLUDE.'/moduleManager.php';
    require_once 'modules.php';
    require_once 'locales.php';
    require_once 'AdminInterface.php';
    
    $smarty->assign('smarty_path', REL_PATH_SMARTY);
    $smarty->assign('status', '');
    
    //the module manager
    $modManager = new ModuleManager($modules);
    
    //check for valid session and save the ip address
    validSession() or die(INVALID_SESSION);
    
    //logged in before if the user ID is set
    if(isset($_SESSION['UID'])) {
        $login = True;
    }
    else {     //not logged in yet
        $login = False;
    }
    //logout
    if (isset($_GET['action']) AND  $_GET['action'] == 'logout') {
        $login = False;
        session_destroy(); 
    }
    //login   
    if(!$login) {
    	///@todo the variable modules is used in login.php, but is declared in modules.php, thus very confusing. Refactor needed!
        require_once "login.php";    
    }
    
    //login.php sets $login to true so this is executed after a successful log-in
    if($login) {
        $smarty->assign('username', $_SESSION['username']);
        $smarty->assign('sid', htmlspecialchars(SID));
        $smarty->assign('base_path', PATH_SMARTY.'/templates/administrator/base_layout.tpl');
        //include a module if selected
        if (isset($_GET['section'])) {
            $modManager->execute($_GET['section']);
        }
        //if the user only has access to one module, include that 
        elseif ($modManager->checkForSingleModule()){}  
        //or include the menu
        else {
            $allowedModules = array();
            foreach ($modules as $module) {
                if($_SESSION['modules'][$module]) {
                    $allowedModules[] = $module;
                }
            } 
            
        	$smarty->assign('is_mainmenu', true);
            $smarty->assign('modules', $allowedModules);
            $smarty->assign('module_names', $module_names);
            $smarty->display('administrator/menu.tpl');   
        }
    }

?>