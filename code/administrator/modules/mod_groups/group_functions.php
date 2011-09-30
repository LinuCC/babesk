<?php 
	function new_group() {
		require_once PATH_INCLUDE.'/group_access.php';
		require_once 'group_constants.php';
		
		global $smarty;
		$groupManager = new GroupManager(); 
		
		
		if(isset($_POST['groupname'], $_POST['max_credit'])){
			$groupname = $_POST['groupname'];
			$max_credit = $_POST['max_credit'];
			
			if(!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $max_credit)) {
				die(ERR_INP_MAX_CREDIT.' '.$max_credit);
			}
			$max_credit = str_replace(',', '.', $max_credit);
			
			$groupManager->addGroup($groupname,$max_credit);
			echo 'Gruppe "'.$groupname.'", maximales Guthaben:"'.$max_credit
				.'", wurde hinzugefügt';
		}
		else {
			$smarty->display(PATH_SMARTY.'/templates/administrator/modules/mod_groups/form_new_group.tpl');
		}
	}

?>