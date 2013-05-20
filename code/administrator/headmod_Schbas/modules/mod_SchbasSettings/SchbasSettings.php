<?php

require_once PATH_INCLUDE . '/Module.php';

class SchbasSettings extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {

		defined('_AEXEC') or die('Access denied');

		require_once 'AdminSchbasSettingsInterface.php';
		require_once 'AdminSchbasSettingsProcessing.php';

		$SchbasSettingsInterface = new AdminSchbasSettingsInterface($this->relPath);
		$SchbasSettingsProcessing = new AdminSchbasSettingsProcessing($SchbasSettingsInterface);

		if (!isset($_GET['action']))
			$SchbasSettingsInterface->InitialMenu();
		else {
			switch ($_GET['action']){
				case '1':	$SchbasSettingsInterface->GeneralSettings();break;
				case '2':	$SchbasSettingsInterface->LoanSettings($SchbasSettingsProcessing->getLoanSettings(),false);break;
				case '3':	$SchbasSettingsInterface->RetourSettings();break;
				case '4':	TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $_POST['owner']."|".$_POST['number']."|".$_POST['blz']."|".$_POST['institute'], 'bank_details'));break;
				case '5':	TableMng::query(sprintf("UPDATE schbas_fee SET fee_normal = %s, fee_reduced = %s WHERE grade = '5'",$_POST['5norm'], $_POST['5erm']));
							TableMng::query(sprintf("UPDATE schbas_fee SET fee_normal = %s, fee_reduced = %s WHERE grade = '6'",$_POST['6norm'], $_POST['6erm']));
							TableMng::query(sprintf("UPDATE schbas_fee SET fee_normal = %s, fee_reduced = %s WHERE grade = '7'",$_POST['7norm'], $_POST['7erm']));
							TableMng::query(sprintf("UPDATE schbas_fee SET fee_normal = %s, fee_reduced = %s WHERE grade = '8'",$_POST['8norm'], $_POST['8erm']));
							TableMng::query(sprintf("UPDATE schbas_fee SET fee_normal = %s, fee_reduced = %s WHERE grade = '9'",$_POST['9norm'], $_POST['9erm']));
							TableMng::query(sprintf("UPDATE schbas_fee SET fee_normal = %s, fee_reduced = %s WHERE grade = '10'",$_POST['10norm'], $_POST['10erm']));
							TableMng::query(sprintf("UPDATE schbas_fee SET fee_normal = %s, fee_reduced = %s WHERE grade = '11'",$_POST['11norm'], $_POST['11erm']));
							TableMng::query(sprintf("UPDATE schbas_fee SET fee_normal = %s, fee_reduced = %s WHERE grade = '12'",$_POST['12norm'], $_POST['12erm']));
							$SchbasSettingsInterface->LoanSettings($SchbasSettingsProcessing->getLoanSettings(),true);break;
				case '6':	$claim_date = $_POST['claim_Day'].".". $_POST['claim_Month'].".". $_POST['claim_Year'];
							$transfer_date = $_POST['transfer_Day'].".". $_POST['transfer_Month'].".". $_POST['transfer_Year'];
							TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $claim_date,"schbasDeadlineClaim"));
							TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $transfer_date,"schbasDeadlineTransfer"));break;
			}	
		}
	}
}

?>