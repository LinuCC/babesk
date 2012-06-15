<?php

class AdminGroupProcessing {
	function __construct ($groupInterface) {

		require_once PATH_ACCESS . '/GroupManager.php';
		require_once 'AdminGroupInterface.php';
		$this->groupManager = new GroupManager();
		$this->groupInterface = $groupInterface;
		$this->msg = array(
			//NewGroup
			'err_group_exists'		 => 'Die Gruppe ist schon vorhanden',
			'err_inp_max_credit'	 => 'falsche Eingabe des maximalen Guthabens',
			'err_inp_groupname'		 => 'falsche Eingabe des Gruppennamens',
			'err_inp_price'			 => 'falsche Eingabe des Preises',
			'err_add_group'			 => 'konnte die Gruppe nicht hinzufügen',
			'err_add_pc'			 =>
				'Konnte eine Preisklasse nicht hinzufügen. Möglicherweise ist der Gruppeneintrag in der Datenbank nun fehlerhaft!',
			'err_fetch_pc'			 => 'Ein Fehler ist beim holen der Preisklassen aufgetreten',
			'err_no_pc'				 => 'Es ist keine Preisklasse vorhanden. Bitte erstellen sie zuerst Preisklassen!',
			'fin_add_group_wo_pc'	 => 'Gruppe "%s", maximales Guthaben:"%s", wurde ohne Preisklassen hinzugefügt',
			'fin_add_group'			 => 'Gruppe "%s", maximales Guthaben:"%s", wurde hinzugefügt',
			//DeleteGroup
			'err_del_group'			 => 'konnte die Gruppe nicht löschen',
			'err_del_pc'			 =>
				'konnte die zur Gruppe zugehörigen Preisklassen nicht löschen. Möglicherweise sind einige Datenbankeinträge nun fehlerhaft!',
			'fin_del_group'			 => 'Die Gruppe wurde erfolgreich gelöscht',
			//ChangeGroup
			'err_change_group'		 => 'Fehler beim Ändern der Gruppendaten',
			'fin_change_group'		 => 'Die Gruppe wurde erfolgreich verändert.',
			'err_inp_id'			 => 'falsche Eingabe der ID',
			'err_get_data_group'	 => 'Fehler beim fetchen der Gruppendaten aus dem MySQL-Server', );
	}

	/**
	 * NewGroup adds a new group based on some POST-Variables
	 * It needs the POST-Variables groupname and max_credit. ID is done by MySQL's
	 * auto-incrementing id. Additionally it will add Priceclasses for the group.
	 *
	 * @todo Update the description
	 * @see GroupManager
	 */
	function NewGroup () {

		require_once PATH_ACCESS . '/PriceClassManager.php';

		$pcManager = new PriceClassManager();

		/**
		 * Add a new group to the MySQL-table
		 */
		if (isset($_POST['groupname'], $_POST['max_credit'])) {
			/**
			 * add Group
			 */
			$groupname = $_POST['groupname'];
			$max_credit = $_POST['max_credit'];

			//error-checking
			if (!isset($groupname) || $groupname == '')
				$this->groupInterface->dieError($this->msg['err_inp_groupname']);

			if ($this->groupManager->existsGroupName($groupname))
				$this->groupInterface->dieError($this->msg['err_group_exists']);

			if (!isset($max_credit) || $max_credit == '' || !preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/',
				$max_credit))
				$this->groupInterface->dieError($this->msg['err_inp_max_credit'] . ' ' . $max_credit);

			$max_credit = str_replace(',', '.', $max_credit);

			/**
			 * add Priceclasses belonging to the group
			 */

			if (isset($_POST['n_price'])) {
				/** Fetches the Priceclasses from the Server. For every Priceclass-ID there is a POST-Variable from the
				 * create_group-form with name and ID.
				 */
				try {
					$priceclasses = $pcManager->getAllPriceClassesPooled();
				} catch (MySQLVoidDataException $e) {
					$this->groupInterface->dieMsg(sprintf($this->msg['fin_add_group_wo_pc'], $groupname, $max_credit));
				}
				catch (Exception $e) {
					$this->groupInterface->dieError($this->msg['err_fetch_pc']);
				}

				//pc_to_add_arr: If a problem happens during the adding-loop, we need to be safe that no entries are added yet
				$pc_to_add_arr = array();
				$standard_price = $_POST['n_price'];

				//check standardprice-input
				try {
					inputcheck($standard_price, 'credits', 'StandardPreis');
				} catch (Exception $e) {
					$this->groupInterface->dieError($this->msg['err_inp_price'] . ' in:' . $e->getFieldName());
				}

				//add the priceclasses
				foreach ($priceclasses as $priceclass) {
					try {
						$pc_id = $priceclass['pc_ID'];
						$pc_name = $_POST['pc_name' . $pc_id];
						$pc_price = $_POST['pc_price' . $pc_id];
						//groupID will be added after the data-checking, so the next ID of MySQL's Autoincrement is the groupID
						$group_id = $this->groupManager->getNextAutoIncrementID();

						if (!isset($pc_price) || !$pc_price || $pc_price == '')
							$pc_price = $standard_price;

						try {
							//check for correct input of price
							inputcheck($pc_price, 'credits', $pc_name);
						} catch (WrongInputException $e) {
							$this->groupInterface->dieError($this->msg['err_inp_price'] . ' in:' . $e->getFieldName());
						}

						$pc_to_add_arr[] = array(
							'name'		 => $pc_name,
							'gid'		 => $group_id,
							'pc_price'	 => $pc_price,
							'pid'		 => $pc_id);
					} catch (Exception $e) {
						$this->groupInterface->dieError('A Priceclass with the ID ' . $pc_id . ' could not be added: '
							. $e->getMessage());
					}
				}
			}

			/**
			 * finish adding Group and Priceclass
			 */
			try {
				$this->groupManager->addGroup($groupname, $max_credit);
			} catch (Exception $e) {
				$this->groupInterface->dieError($this->msg['err_add_group']);
			}
			
			if (isset($_POST['n_price'])) {
				foreach ($pc_to_add_arr as $pc_to_add) {
					try {
						$pcManager->addPriceClass($pc_to_add['name'], $pc_to_add['gid'], $pc_to_add['pc_price'],
							$pc_to_add['pid']);
					} catch (Exception $e) {
						$this->groupInterface->dieError($this->msg['err_add_pc']);
					}
				}
			}

			$this->groupInterface->dieMsg(sprintf($this->msg['fin_add_group'], $groupname, $max_credit));
		}
		/**
		 * Show a form to create a new group
		 */
		else {
			try {
				$pc_arr = $pcManager->getAllPriceClassesPooled();
			} catch (MySQLVoidDataException $e) {
				$pc_arr = false;
			}
			$this->groupInterface->NewGroup($pc_arr);
			}}

		/**
		 * This function deletes a Group from the MySQL-table based on the given parameter ID
		 * @param numeric_string $ID
		 */
		function DeleteGroup ($ID) {

			require_once PATH_ACCESS . '/PriceClassManager.php';

			$pcManager = new PriceClassManager();

			try { //delete the priceclass
				$this->groupManager->delEntry($ID);
			} catch (Exception $e) {
				$this->groupInterface->dieError($this->msg['err_del_group'] . ' :' . $e->getMessage());
			}
			try { //delete priceclasses which are connected to the groups
				$priceclasses = $pcManager->getTableData(sprintf('GID = %s', $ID));
			} catch (Exception $e) {
				$this->groupInterface->dieError($this->msg['err_del_pc'] . ' :' . $e->getMessage());
			}
			foreach ($priceclasses as $priceclass) {
				try {
					$pcManager->delEntry($priceclass['ID']);
				} catch (Exception $e) {
					$this->groupInterface->dieError($this->msg['err_del_pc'] . ' :' . $e->getMessage());
				}
			}

			$this->groupInterface->dieMsg($this->msg['fin_del_group']);
		}

		/**
		 * change_group lets the user change group-parameters in the MySQL
		 * It shows a form, which let the user decide what to change. It then deletes the old
		 * group and adds the changed new group into the table.
		 * @param integer/long $ID
		 *
		 * @see GroupManager
		 */
		function ChangeGroup ($ID) {

			//form is filled out
			if (isset($_GET['where'], $_POST['ID'], $_POST['name'], $_POST['max_credit'])) {

				$old_ID = $_GET['where']; //if group moved to new ID, delete the old one
				$ID = $_POST['ID'];
				$name = $_POST['name'];
				$max_credit = $_POST['max_credit'];

				if (!preg_match('/\A^[0-9]{0,2}((,|\.)[0-9]{2})?\z/', $max_credit))
					$this->groupInterface->dieError($this->msg['err_inp_max_credit'] . ' ' . $max_credit);
				if (!is_numeric($ID))
					$this->groupInterface->dieError($this->msg['err_inp_id']);
				try {
					$this->groupManager->alterEntry($old_ID, 'name', $name, 'max_credit', $max_credit, 'ID', $ID);
				} catch (Exception $e) {
					$this->groupInterface->dieError($this->msg['err_change_group']);
				}
				$this->groupInterface->dieMsg($this->msg['fin_change_group']);
			}
			else { //show form

				if (!is_numeric($ID))
					$this->groupInterface->dieError($this->msg['err_inp_id']);

				try {
					$group_data = $this->groupManager->getEntryData($ID, 'ID', 'name', 'max_credit');
				} catch (MySQLVoidDataException $e) {
					$this->groupInterface->dieError($this->msg['err_get_data_group']);
				}
				$this->groupInterface->ChangeGroup($group_data['ID'], $group_data['name'], $group_data['max_credit']);
			}
		}

		/**
		 * shows form with all groups listed in MySQL-table
		 * makes use of group_access.php
		 *
		 * @see GroupManager
		 */
		function ShowGroups () {

			$groups = array();
			try {
				$groups = $this->groupManager->getTableData();
			} catch (Exception $e) {
				$this->groupInterface->dieError($this->msg['err_get_data_group']);
			}
			$this->groupInterface->ShowGroups($groups);
		}

		/**
		 * Allows connection and setting and fetching data from MySQL-Server
		 * @var GroupManager
		 */
		protected $groupManager;

		/**
		 * Allows Output to the user
		 * @var AdminGroupInterface
		 */
		protected $groupInterface;

		/**
		 * Contains Messages shown to the user
		 * @var string[]
		 */
		protected $msg;

	}

?>