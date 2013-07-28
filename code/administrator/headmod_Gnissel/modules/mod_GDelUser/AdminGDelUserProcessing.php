<?php

class AdminGDelUserProcessing {

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	private $cardManager;
	private $userManager;
	private $cardInfoInterface;
	private $msg;

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	public function __construct ($cardInfoInterface) {

		require_once PATH_ACCESS . '/CardManager.php';
		require_once PATH_ACCESS . '/UserManager.php';

		require_once 'AdminGDelUserInterface.php';

		$this->cardManager = new CardManager();
		$this->userManager = new UserManager();
		$this->cardInfoInterface = $cardInfoInterface;

		$this->msg = array(
			'err_card_id'			 => 'Diese Karte ist nicht vergeben!',
			'err_get_user_by_card'	 => 'Anhand der Kartennummer konnte kein Benutzer gefunden werden.',
			'err_no_orders'			 => 'Es sind keine Bestellungen für diesen Benutzer vorhanden.',
			'err_meal_not_found'	 => 'Ein Menü konnte nicht gefunden werden!',
			'err_connection'		 => 'Ein Fehler ist beim Verbinden zum MySQL-Server aufgetreten',
			'msg_order_fetched'		 => 'Die Bestellung wurde schon abgeholt',);
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	/**
	 * Displays the names of all orders for today
	 * @param string $card_id The ID of the Card
	 */
	public function CheckCard ($card_id) {

		if (!$this->cardManager->valid_card_ID($card_id))
			$this->cardInfoInterface->dieError(sprintf($this->msg['err_card_id'], $card_id));

		$uid = $this->GetUser($card_id);
		return  $uid;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	/**
	 * Looks the user for the given CardID up, checks if the Card is locked and returns the UserID
	 * @param string $card_id The ID of the Card
	 * @return string UserID
	 */
	public function GetUser ($card_id) {
		try {
			return $this->cardManager->getUserID($card_id);
		} catch (Exception $e) {
			$this->cardInfoInterface->dieError(sprintf($this->msg['err_card_id'], $card_id));
		}

	}

	/**
	 * Returns some generic user data for identifying a card
	 */
	public function GetUserData($uid) {

		try {
			$data = TableMng::query(sprintf(
				'SELECT u.*,
				(SELECT CONCAT(g.gradeValue, g.label) AS class
					FROM usersInGradesAndSchoolyears uigs
					LEFT JOIN grade g ON uigs.gradeId = g.ID
					WHERE uigs.userId = u.ID AND
						uigs.schoolyearId = @activeSchoolyear) AS class
				FROM users u WHERE ID = %s', $uid), true);

		} catch (MySQLVoidDataException $e) {
			$this->cardInfoInterface->dieError('Der Benutzer wurde nicht gefunden');

		} catch (Exception $e) {
			$this->cardInfoInterface->dieError('Der Benutzer konnte nicht von der Datenbank abgerufen werden!');
		}

		return $data[0];
	}

	public function delUser ($uid) {
		require_once PATH_ACCESS . '/UserManager.php';
		require_once PATH_ACCESS . '/LoanManager.php';

		$userManager = new UserManager();
		$loanManager = new LoanManager();

		try {
			$hasBooks = $loanManager->getLoanlistByUID($uid);
			if (sizeof($hasBooks) != 0)
				$this->cardInfoInterface->DieError('Der Benutzer hat noch B&uuml;cher ausgeliehen!' );
		} catch (Exception $e) {
			$this->cardInfoInterface->DieError('Fehler beim Abrufen der B&uuml;cherliste!');
		}


		$user = array();

		try {

			$temp = $this->GetUserData($uid);
			$bd_temp = explode('-',$temp['birthday']);
			$temp['birthday']=$bd_temp[2].".".$bd_temp[1].".".$bd_temp[0];
			$user = array('forename'=>$temp['forename'],'name'=>$temp['name'],'credit'=>$temp['credit'],'birthday'=>$temp['birthday'],'class'=>$temp['class']);
			//$user = $userManager->getEntryData($uid, 'forename', 'name','credit','birthday','class');


		} catch (Exception $e) {
			$this->cardInfoInterface->dieError('Fehler beim Abrufen der Benutzerdaten.');
		}


		try {
			$userManager->lockAccount($uid);
		} catch (Exception $e) {
			$this->cardInfoInterface->DieError ('Konnte den Benutzer nicht sperren; Ein interner Fehler ist aufgetreten');
		}
		if ($this->createPdf($user,$uid)) {
			$this->cardInfoInterface->ShowDeleteFin($uid);
		}
		else $this->cardInfoInterface->dieError('Fehler beim Generieren des PDFs!');
		//$this->cardInfoInterface->DieMsg ('Der Benutzer wurde erfolgreich gesperrt. L&ouml;schantrag erstellt.');
	}

	public function deletePdf () {
		if (isset ($_GET['ID'])) {
			try {
				unlink (dirname(realpath(''))."/include/pdf/tempPdf/deleted_".$_GET['ID'].".pdf");
				$this->cardInfoInterface->showDeletePdfSuccess ();
			} catch (Exception $e) {
				$this->cardInfoInterface->dieError ('Fehler beim L&ouml;schen des PDFs.');

			}
		}
	}


	/** Creates a PDF for the Message
	 *
	 */
	private function createPdf ($user,$uid) {
		require_once  PATH_INCLUDE .('/pdf/tcpdf/config/lang/ger.php');
		require_once PATH_INCLUDE . '/pdf/tcpdf/tcpdf.php';

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('LeG Uelzen');

		$pdf->SetKeywords('');

		// set default header data
	$pdf->SetHeaderData('../../../../web/headmod_Messages/modules/mod_MessageMainMenu/logo.jpg', 15, 'LeG Uelzen', "Abmeldung von: ".$user['forename']." ".$user['name']." (geb. am ".$user['birthday'].")\nKlasse: ".$user['class'], array(0,0,0), array(0,0,0));
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
		$pdf->SetFont('helvetica', '', 11, '', true);

		// Add a page
		// This method has several options, check the source code documentation for more information.
		$pdf->AddPage();

		// set text shadow effect
		$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

		// Set some content to print
		$html = '<p align="center"><h2>R&uuml;ckgabe der LeG-Card / L&ouml;schung der Benutzerdaten</h2></p><br>'
				.'Hiermit wird best&auml;tigt, dass die Schulb&uuml;cher von '.$user['forename'].' '.$user['name'].' vollst&auml;ndig zur&uuml;ckgegeben wurden. <br/>
Hiermit wird best&auml;tigt, dass s&auml;mtliche personenbezogenen Daten am '.date("d.m.Y").' aus dem System gel&ouml;scht wurden.<br/>';

		if ($user['credit']=="0.00") $html .= 'Es liegt kein Restguthaben vor.<br/>';
		else $html .= 'Es liegt ein Restguthaben in H&ouml;he von '.$user['credit'].' &euro; vor. Dieses muss beim Caterer abgeholt werden.<br/>';
		$html .= 'Mit der R&uuml;ckgabe der LeG-Card kann das Pfandgeld in H&ouml;he von 3,50 &euro; zzgl. 0,50 &euro;, je nach Zustand der H&uuml;lle, ausbezahlt werden.<br/>
Dieses Schreiben wurde maschinell erstellt und ist ohne Unterschrift g&uuml;ltig.
				<hr>
<p align="center"><h3>Auszahlung des Restguthabens</h3></p><br>
Restguthaben in H&ouml;he von '.$user['credit'].' &euro; am ___.___.2013 ausgezahlt.<br><br>
<br>						Unterschrift Caterer
		<br>
		<hr>
<p align="center"><h3>Abschnitt f&uuml;r den Caterer</h3></p><br>
 Restguthaben in H&ouml;he von '.$user['credit'].' &euro; am ___.___.2013 erhalten.<br><br>
		<br><br>Unterschrift '.$user['forename'].' '.$user['name'].' (geb. am '.$user['birthday'].')
		<hr>
<p align="center"><h3>Pfanderstattung</h3></p><br>
Bitte geben Sie diesen Abschnitt im Gnissel-B&uuml;ro im Lessing-Gymnasium ab.<br>
Bitte kreuzen Sie an, ob Sie den Pfandbetrag an die Sch&uuml;lergenossenschaft Gnissel des LeG Uelzen spenden m&ouml;chten
		oder eine &Uuml;berweisung auf ein Bankkonto w&uuml;nschen.<br>

[&nbsp;&nbsp;] Das Pfandgeld m&ouml;chte ich an Gnissel spenden<br>
[&nbsp;&nbsp;] Ich m&ouml;chte das Pfandgeld auf folgendes Konto &uuml;berwiesen haben:<br>
Kontoinhaber:   <br>
Kontonummer:<br>
BLZ:		<br>
Kreditinstitut: <br><br>

Uelzen, den ___.___.2013
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Unterschrift '.$user['forename'].' '.$user['name'].' (geb. am '.$user['birthday'].')<br>



		';

		// Print text using writeHTMLCell()
		$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		// ---------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$pdf->Output('../include/pdf/tempPdf/deleted_'.$uid.'.pdf', 'F');
		return true;
	}

}

?>
