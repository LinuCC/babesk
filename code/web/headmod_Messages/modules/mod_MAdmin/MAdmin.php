<?php

require_once PATH_INCLUDE . '/Module.php';

class MAdmin extends Module {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $smartyPath;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct($name, $display_name, $path) {
		parent::__construct($name, $display_name, $path);
		$this->smartyPath = PATH_SMARTY . '/templates/web' . $path;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	public function execute($dataContainer) {
		//No direct access
		defined('_WEXEC') or die("Access denied");

		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/GroupManager.php';
		require_once PATH_ACCESS . '/GlobalSettingsManager.php';


		global $smarty;
		$this->_interface = new WebInterface($smarty);
		$userManager = new UserManager();
		$groupManager = new GroupManager();
		$gsm = new GlobalSettingsManager();
		require_once PATH_INCLUDE . '/TableMng.php';
		TableMng::init ();

		$contracts = "";
		$editor=false;
		$contractGID = TableMng::query('SELECT value FROM global_settings WHERE name LIKE "contracts_edit"',true);

		$userGID = TableMng::query('SELECT GID FROM users WHERE ID LIKE "'.$_SESSION['uid'].'"',true);

		($contractGID[0]['value'] == $userGID[0]['GID'])? $editor=true : $editor=false;

		if (isset($_GET['action'])) {

		$action=$_GET['action'];
		switch ($action) {
			case 'newcontract':
				$classesRaw = TableMng::query('SELECT DISTINCT CLASS FROM users ',true);

				foreach($classesRaw as $class) {
					$classes[] = $class['CLASS'];
				}

				$smarty->assign('classes',$classes);
				$smarty->display($this->smartyPath . 'new_contract.tpl');
				break;

			case 'savecontract':
				$class = implode('|', $_POST['class']);
				TableMng::query(sql_prev_inj(sprintf('INSERT INTO contracts (author_id,class,title,text,valid_from,valid_to) VALUES (%s,"%s","%s","%s","%s","%s")',$_SESSION['uid'],$class,$_POST['contracttitle'],$_POST['contracttext'],
						$_POST['StartDateYear'] . '-' . $_POST['StartDateMonth'] . '-' .
							$_POST['StartDateDay'], $_POST['EndDateYear'] . '-' . $_POST['EndDateMonth'] . '-' . $_POST[
							'EndDateDay'])));
				$smarty->display($this->smartyPath . 'new_contract_fin.tpl');
				break;

			case 'deletecontract':

				$authorID = TableMng::query(sql_prev_inj(sprintf('SELECT author_id FROM contracts WHERE id="%s"',$_GET['id'])),true);

				if ($editor && ($authorID[0]['author_id']==$_SESSION['uid'])) {
				TableMng::query(sql_prev_inj(sprintf("DELETE FROM contracts WHERE id='%s'",$_GET['id'])));

				}
				$smarty->display($this->smartyPath . 'delete_contract_fin.tpl');
				break;

			case 'showcontract':
				$contractClass = TableMng::query('SELECT class FROM contracts WHERE id="'.$_GET['id'].'"',true);
				$userClass = TableMng::query('SELECT class FROM users WHERE id="'.$_SESSION['uid'].'"',true);
				if (!$editor && !strstr($contractClass[0]['class'],$userClass[0]['class'])) {
					$this->_interface->dieError (
							'Kein Zugriff erlaubt!');
				}
				$contract = TableMng::query("SELECT title,text FROM contracts WHERE id = ".$_GET['id'],true);
				$forename = TableMng::query('SELECT forename FROM users WHERE ID = '.$_SESSION['uid'],true);
				$name = TableMng::query('SELECT name FROM users WHERE ID = '.$_SESSION['uid'],true);
				$class = TableMng::query('SELECT class FROM users WHERE ID = '.$_SESSION['uid'],true);

				$contract[0]['text'] = str_replace("{vorname}",$forename[0]['forename'], $contract[0]['text']);
				$contract[0]['text'] = str_replace("{name}",$name[0]['name'], $contract[0]['text']);

					$this->createPdf($contract[0]['title'],$contract[0]['text'],$class[0]['class']);
					break;
			}
		}
		else {

		if ($editor) {
			$query = 'SELECT c.id,c.title,c.class,c.valid_from,c.valid_to FROM contracts AS c WHERE c.author_id LIKE "'.$_SESSION['uid'].'"';

			$smarty->assign('editor',true);
			try {
				$contracts = TableMng::query ($query, true);
				$smarty->assign('valid_from',  formatDate($contracts[0]['valid_from']));
				$smarty->assign('valid_to',  formatDate($contracts[0]['valid_to']));
			} catch (MySQLVoidDataException $e) {
				$smarty->assign('error','Konnte keine Vorlagen finden');
			} catch (Exception $e) {
				$this->_interface->dieError (
						sprintf ('Konnte die Vorlagen nicht abrufen!', $e->getMessage()));
			}
		} else {
			$class = TableMng::query('SELECT class FROM users WHERE ID LIKE "'.$_SESSION['uid'].'"',true);

			$contracts= "SELECT c.id,c.title,c.class,c.valid_from,c.valid_to FROM contracts AS c WHERE c.class LIKE '%".$class[0]['class']."%' AND SYSDATE() BETWEEN c.valid_from AND c.valid_to";

			try {
				$contracts = TableMng::query ($contracts, true);
				$smarty->assign('valid_from',  formatDate($contracts[0]['valid_from']));
				$smarty->assign('valid_to',  formatDate($contracts[0]['valid_to']));
			} catch (MySQLVoidDataException $e) {
				$smarty->assign('error','Keine Post vorhanden!');
			} catch (Exception $e) {
				$this->_interface->dieError (
						sprintf ('Konnte die Post nicht abrufen!', $e->getMessage()));
			}
		}


		$smarty->assign('contracts', $contracts);

		if (preg_match("/BaBeSK/i", $_SERVER['HTTP_USER_AGENT'])) {
			$smarty->assign('BaBeSkTerminal',true);
		} else {
			$smarty->assign('BaBeSkTerminal',false);
		}

		$smarty->display($this->smartyPath . 'menu.tpl');
		}
	}


	/** Creates a PDF for the Message 
	 *
	 */
	private function createPdf ($title,$text,$class) {
		require_once  PATH_INCLUDE .('/pdf/tcpdf/config/lang/ger.php');
		require_once PATH_INCLUDE . '/pdf/tcpdf/tcpdf.php';

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('LeG Uelzen');
		$pdf->SetTitle($title);
		$pdf->SetSubject($title);
		$pdf->SetKeywords('');

		// set default header data
		$pdf->SetHeaderData('../../../../web/headmod_Messages/modules/mod_MAdmin/logo.jpg', 15, 'LeG Uelzen', "Formulargenerator 0.1\nKlasse: ".$class, array(0,0,0), array(0,0,0));
		$pdf->setFooterData($tc=array(0,0,0), $lc=array(0,0,0));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// ---------------------------------------------------------

		// set default font subsetting mode
		$pdf->setFontSubsetting(true);

		// Set font
		// dejavusans is a UTF-8 Unicode font, if you only need to
		// print standard ASCII chars, you can use core fonts like
		// helvetica or times to reduce file size.
		$pdf->SetFont('helvetica', '', 14, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

		// Set some content to print
		$html = '<p align="center"><h2>'. $title.'</h2></p><br>'.$text;

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('example_001.pdf', 'I');
	}
}
?>