<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");
    global $smarty;
    
    $userManager = new UserManager();

	if(isset($_POST['passwd'])) {
	    
	    if (!isset($_POST['passwd']) and !isset($_POST['passwd_repeat'])) {
		  die(INVALID_FORM);
		}
		$passwd = '';
		if(($passwd = $_POST['passwd']) != $_POST['passwd_repeat']) {
          $smarty->assign('status', UNMATCHED_PASSWORDS);
          $smarty->display('web/modules/mod_change_password/change_password.tpl');
          exit();
        }
        
        if(!preg_match('/\A^[a-zA-Z0-9 _-]{4,20}\z/',$passwd)){
		  $smarty->assign('status', '<p class="error">Das Passwort muss mindestens 4 Zeichen lang sein und darf keine Sonderzeichen enthalten</p>');
		  $smarty->display('web/modules/mod_change_password/change_password.tpl');
          exit();
	   }

	    $userManager->updatePassword($_SESSION['uid'], $passwd);
	    
	    $smarty->assign('status', '<p>Erstpasswort wurde erfolgreich ge&auml;ndert</p>');
	}
    else {
	    $smarty->display('web/modules/mod_change_password/change_password.tpl');
	    exit();
	}
?>