<?php
    //No direct access
    defined('_WEXEC') or die("Access denied");
    
    global $smarty;
    

	$userManager = new UserManager();

	if(isset($_POST['kontoSperren']) && $_POST['kontoSperren'] == 'lockAccount') {
		$userManager->lockAccount($_SESSION['uid']);

		$smarty->assign('status', '<p>Konto wurde erfolgreich gesperrt.</p>');
		header('Location: index.php?action=logout');
		
	}
	else {
		
		$smarty->display("web/modules/mod_account/account.tpl");
		exit();
	}
	//$smarty->display("web/modules/mod_account/account.tpl");
	?>