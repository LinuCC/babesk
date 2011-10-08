<?php 

	/**
	 * new_group() adds a new group based on some POST-Variables 
	 * needs the POST-Variables groupname and max_credit. ID is done by MySQL's
	 * auto-incrementing id.
	 * 
	 * @see GroupManager
	 */
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
	
	/**
	 * delete_group deletes a group
	 * The function makes use of group_access.php and deletes a group
	 * 
	 * @see GroupManager
	 * 
	 * @param integer/long $ID the ID of the Group to delete
	 */
	function delete_group($ID) {
		require_once PATH_INCLUDE.'/group_access.php';
		require_once 'group_constants.php';
		
		$groupManager = new GroupManager();
		if(!is_numeric($ID))die(ERR_INP_ID);
		
		$is_deleted = $groupManager->delGroup($ID);
		if(!$is_deleted)echo ERR_DEL_GROUP;
		else echo GROUP_DELETED;
	}
	
	/**
	 * change_group lets the user change group-parameters in the MySQL
	 * It shows a form, which let the user decide what to change. It then deletes the old
	 * group and adds the changed new group into the table.
	 * @param integer/long $ID
	 * 
	 * @see GroupManager
	 * 
	 * @todo there should be a MySQL-change-function, deleting and adding is not data-save
	 */
	function change_group($ID) {
		require_once PATH_INCLUDE.'/group_access.php';
		require_once 'group_constants.php';
		
		//form is filled out
		if(isset($_GET['where'], $_POST['ID'],$_POST['name'],$_POST['max_credit'])){
			$groupManager = new GroupManager();
			$old_ID = $_GET['where'];//if group moved to new ID, delete the old one
			$ID = $_POST['ID'];
			$name = $_POST['name'];
			$max_credit = $_POST['max_credit'];
			
			if(!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $max_credit))
				die(ERR_INP_MAX_CREDIT.' '.$max_credit);
			if(!is_numeric($ID))
				die(ERR_INP_ID);
			
			if($old_ID == $ID) {//only delete entry first if the ID is identical
				$is_deleted = $groupManager->delGroup($old_ID);
				if(!$is_deleted)die(ERR_DEL_GROUP);
			}
			else {//otherwise it could be a duplicated ID in MySQL, be save and DONT delete entry first
				$is_added = $groupManager->addGroup($name, $max_credit, $ID);
				if(!$is_added)die(ERR_ADD_GROUP);
				$is_deleted = $groupManager->delGroup($old_ID);
				if(!$is_deleted)die(ERR_DEL_GROUP);
			}
			
			echo GROUP_CHANGED;
		}
		else { //show form
			$groupManager = new GroupManager();
			global $smarty;
			if(!is_numeric($ID))die(ERR_INP_ID);
			
			$groups = $groupManager->getGroupData($ID, 'ID', 'name','max_credit');
			if(!$groups)die(ERR_GET_DATA_GROUP);
			//getGroupData gives in every case nto more then one group back, no need for array
			foreach($groups as $group)$group_data = $group;
			
			$smarty->assign('ID', $group_data['ID']);
			$smarty->assign('name', $group_data['name']);
			$smarty->assign('max_credit', $group_data['max_credit']);
			$smarty->display(PATH_SMARTY_ADMIN_MOD.'/mod_groups/change_group.tpl');
		}
	}
	
	/**
	 * shows form with all groups listed in MySQL-table
	 * makes use of group_access.php
	 * 
	 * @see GroupManager
	 */
	function show_groups() {
		require_once PATH_INCLUDE.'/group_access.php';
		
		$groupManager = new GroupManager();
		global $smarty;
		
		$groups = array();
		$groups = $groupManager->getGroupData();
		
		$smarty->assign('groups', $groups);
		$smarty->display(PATH_SMARTY.'/templates/administrator/modules/mod_groups/show_groups.tpl');
	}

?>