<?php

    //no direct access
    defined('_AEXEC') or die("Access denied");

	require_once PATH_INCLUDE."/admin_access.php";
	$adminManager = new AdminManager();
	
	global $smarty;
	global $module_names;
	
	require 'modules.php';
	
	
	//the different actions the module can do
	$_addAdmin = 'addAdmin';
	$_delAdmin  = 'delAdmin';
	$_addAdminGroup = 'addAdminGroup';
	$_delAdminGroup = 'delAdminGroup';

    if(isset($_GET['action'])) {
        if($_GET['action'] == $_addAdmin) {
            if ('POST' == $_SERVER['REQUEST_METHOD']) {
        	    if (!isset($_POST['adminname'], $_POST['password'], $_POST['group'])) {
        		   die(INVALID_FORM);
        	    } 
        	    if (('' == $adminname = trim($_POST['adminname'])) OR
            		('' == $password = trim($_POST['password'])) OR
            		('' == $groupname = trim($_POST['group']))) {
            		die(EMPTY_FORM);
        		}
        		$groupid = $adminManager->getAdminGroupID($groupname);
        		
        		if($adminManager->addAdmin($adminname, $password, $groupid)) {
        			$smarty->assign('msg', '<p>Admin "'.$adminname.'" wurde erfolgreich hinzugef&uuml;gt</p>');    
        		}
                else {
                    $smarty->assign('msg', '<p>Vorgang Fehlgeschlagen! :(</p>');
                }
                $smarty->display('administrator/modules/mod_admins/msg.tpl');               
   
            }
            else {
	            //include the template
	            $smarty->assign('admin_groups', $adminManager->getAdminGroups());
                $smarty->display('administrator/modules/mod_admins/addAdmin.tpl');
            }
        }
        if($_GET['action'] == $_delAdmin) {
        	if ('POST' == $_SERVER['REQUEST_METHOD']) {
        	    if (!isset($_POST['adminname'])) {
        		   die(INVALID_FORM);
        	    }   
        	    if (('' == $adminname = trim($_POST['adminname']))) {
            		die(EMPTY_FORM);
        		}                   
   				$adminid = $adminManager->getAdminID($adminname);
   				
   				if($adminManager->delAdmin($adminid)) {
   					$smarty->assign('msg', '<p>Admin "'.$adminname.'" wurde erfolgreich gel&ouml;scht</p>');
   				}
   				else {
                    $smarty->assign('msg', '<p>Vorgang Fehlgeschlagen! :(</p>');
                }
                $smarty->display('administrator/modules/mod_admins/msg.tpl');
            }
            else {
	            //include the template
	            $smarty->assign('admins', $adminManager->getAdmins());
                $smarty->display('administrator/modules/mod_admins/delAdmin.tpl');
            }    
        }
        if($_GET['action'] == $_addAdminGroup) {
            if ('POST' == $_SERVER['REQUEST_METHOD']) {
        	    if (!isset($_POST['groupname'], $_POST['modules'])) {
        		   die(INVALID_FORM);
        	    } 
        	    if (('' == $groupname = trim($_POST['groupname']))) {
            		die(EMPTY_FORM);
        		}                
                $module_string = implode(', ', $_POST['modules']); 
       		
        		if($adminManager->addAdminGroup($groupname, $module_string)) { 
        			$smarty->assign('msg', '<p>Admin Gruppe "'.$groupname.'" wurde erfolgreich hinzugef&uuml;gt</p>');
        		}
                else {
                    $smarty->assign('msg', '<p>Vorgang Fehlgeschlagen! :(</p>');
                }
                $smarty->display('administrator/modules/mod_admins/msg.tpl');          
   
            }
            else {
	            //include the template
	            $smarty->assign('modules', $modules);
	            $smarty->assign('module_names', $module_names);
                $smarty->display('administrator/modules/mod_admins/addAdminGroup.tpl');
            }
        }
        if($_GET['action'] == $_delAdminGroup) {
        	if ('POST' == $_SERVER['REQUEST_METHOD']) {
        	   if (!isset($_POST['group'])) {
        		   die(INVALID_FORM);
        	    }   
        	    if (('' == $groupname = trim($_POST['group']))) {
            		die(EMPTY_FORM);
        		}                   
   				$groupid = $adminManager->getAdminGroupID($groupname);
   				
   				if($adminManager->delAdminGroup($groupid)) {
   					$smarty->assign('msg', '<p>Admin Gruppe "'.$groupname.'" wurde erfolgreich gel&ouml;scht</p>');   
   				}
                else {
                    $smarty->assign('msg', '<p>Vorgang Fehlgeschlagen! :(</p>');
                }
                $smarty->display('administrator/modules/mod_admins/msg.tpl');              
   
            }
            else {
	            //include the template
	            $smarty->assign('admin_groups', $adminManager->getAdminGroups());
                $smarty->display('administrator/modules/mod_admins/delAdminGroup.tpl');
            }    
        }
    }
	else {
	    //include the template
        $smarty->display('administrator/modules/mod_admins/admins.tpl');
    }

?>