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
				case '5':	$this->updateFee();
							$SchbasSettingsInterface->LoanSettings($SchbasSettingsProcessing->getLoanSettings(),true);break;
				case '6':	$claim_date = $_POST['claim_Day'].".". $_POST['claim_Month'].".". $_POST['claim_Year'];
							$transfer_date = $_POST['transfer_Day'].".". $_POST['transfer_Month'].".". $_POST['transfer_Year'];
							TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $claim_date,"schbasDeadlineClaim"));
							TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", $transfer_date,"schbasDeadlineTransfer"));break;
				case '7':	$claimEnabled = TableMng::query(sprintf("SELECT value FROM global_settings WHERE name='isSchbasClaimEnabled'"), true);
							$SchbasSettingsInterface->enableFormConfirm($claimEnabled[0]['value']);break;
				case '8':   $SchbasSettingsInterface->TextSettings();break;
				case '9':	if (isset($_POST['enable'])){
							TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", 1, 'isSchbasClaimEnabled'));
							}else{
					TableMng::query(sprintf("UPDATE global_settings SET value = '%s' WHERE name = '%s'", 0, 'isSchbasClaimEnabled'));
				}
				$SchbasSettingsInterface->enableFormConfirmFin();break;

				case 'fetchTextsAjax':
					$this->fetchTextsAjax();
					break;
			}
		}
	}
	
	function updateFee(){
		$stmt = TableMng::getDB()->prepare("UPDATE schbas_fee SET fee_normal = ?, fee_reduced = ? WHERE grade = ?;");
		for ($i = 5;$i<=12; $i++){
			$_POST[$i.'norm'] = str_replace (",", ".", $_POST[$i.'norm'] );
			$_POST[$i.'erm'] = str_replace (",", ".", $_POST[$i.'erm'] );
			$stmt->bind_param('sss', $_POST[$i.'norm'], $_POST[$i.'erm'], $i);
			if(!$stmt->execute()) {
				die('schinken');
			}
		}
		$stmt->close();
	}

	protected function fetchTextsAjax() {

		$templateId = TableMng::getDb()->real_escape_string($_POST['templateId']);
		$textId = TableMng::getDb()->real_escape_string($_POST['textId']);
		try {
			$template = TableMng::query(sprintf(
					'SELECT * FROM schbas_texts WHERE `description` = "%s%s"',
					$textId,$templateId), true);

		} catch (Exception $e) {
			die('errorFetchTemplate');
		}

		die(json_encode($template[0]));
	}

}

?>