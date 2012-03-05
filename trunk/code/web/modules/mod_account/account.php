<?php
//No direct access
defined('_WEXEC') or die("Access denied");

global $smarty;


$userManager = new UserManager();

if(isset($_POST['kontoSperren']) && $_POST['kontoSperren'] == 'lockAccount') {
	try {
		$userManager->lockAccount($_SESSION['uid']);
	} catch (Exception $e) {
		die('<p class="error">Ein Problem beim Sperren des Accounts ist aufgetreten!</p>');
	}

	$smarty->assign('status', '<p>Konto wurde erfolgreich gesperrt.</p>');
	header('Location: index.php?action=logout');
}
else {
	$smarty->display("web/modules/mod_account/account.tpl");
	exit();
}
?>