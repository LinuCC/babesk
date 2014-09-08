<?php

require_once  PATH_INCLUDE .('/pdf/tcpdf/config/lang/ger.php');
require_once PATH_INCLUDE . '/pdf/tcpdf/tcpdf.php';

class LoanSystemPdf {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($page1Title,$page1Text,$page2Title,$page2Text,$page3Title,$page3Text,$gradeLevel,$msgReturn,$loanChoice,$uid) {

		$this->_page1Title = $page1Title;
		$this->_page1Text = $page1Text;
		$this->_page2Title = $page2Title;
		$this->_page2Text = $page2Text;
		$this->_page3Title = $page3Title;
		$this->_page3Text = $page3Text;
		$this->_gradeLevel = $gradeLevel;
		$this->_msgReturn = $msgReturn;
		$this->_loanChoice = $loanChoice;
		$this->_uid = $uid;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////


	/**
	 * Returns the TCPDF-Object used by this class
	 *
	 * @return TCPDF the TCPDF-Object
	 */
	public function getPdf() {

		return $this->_pdf;
	}

	/**
	 * Sets the TCPDF-Object used by this class
	 *
	 * @param TCPDF $pdf The TCPDF-Object
	 */
	public function setPdf($pdf) {

		$this->_pdf = $pdf;
		return $this;
	}

	/**
	 * Creates the PDF
	 *
	 * @return void
	 */
	public function create() {

		require_once  PATH_INCLUDE .'/pdf/tcpdf/config/lang/ger.php';
		require_once PATH_INCLUDE . '/pdf/tcpdf/tcpdf.php';

		// create new PDF document
		$this->_pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT,
			PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$this->pdfMetadataSet();
		$this->_pdf->AddPage();
		$this->contentPrint($this->_page1Title,$this->_page1Text);
		if ($this->_page2Title!='') {
		$this->_pdf->AddPage();
		$this->contentPrint($this->_page2Title,$this->_page2Text);
		if ($this->_page3Title!='') {
		$this->_pdf->AddPage();
		$this->contentPrint($this->_page3Title,$this->_page3Text);
		}}
	}

	/**
	 * Closes the PDF and outputs it to the User, who can download it
	 *
	 * @return void
	 */
	public function output() {
		$pdfName = sprintf('schbas_%s.pdf', $this->_uid);
		$this->_pdf->Output($pdfName, 'D');
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Sets the Metadata
	 *
	 * @return void
	 */
	protected function pdfMetadataSet() {

		$logoPath = '../../../../web/headmod_Messages/modules/mod_MessageMainMenu/logo.jpg';
		$headerText = sprintf("Schulbuchausleihe 1.0\nJahrgang: %s", $this->_gradeLevel);

		$this->docInformationSet('LeG Uelzen');
		$this->headerDetailsSet($logoPath, 'LeG Uelzen', $headerText);
		$this->footerDetailsSet();
		$this->pageDetailsSet();
	}

	/**
	 * Sets the Information of the document
	 *
	 * @param  string $author The author of the document
	 * @param  string $keywords
	 * @param  string $creator
	 * @return void
	 */
	protected function docInformationSet($author = '',
		$keywords = '', $creator = PDF_CREATOR) {

		$this->_pdf->SetCreator($creator);
		$this->_pdf->SetAuthor($author);
		$this->_pdf->SetTitle('Schulbuchausleihe');
		$this->_pdf->SetSubject('');
		$this->_pdf->SetKeywords($keywords);
	}

	/**
	 * Sets the details of the header
	 *
	 * @param  string $headerLogo the Path to the Logo to print onto the header
	 * @param  string $headerHeading A heading right to the logo
	 * @param  string $headerText Text right to the logo under the heading
	 * @return void
	 */
	protected function headerDetailsSet($headerLogo, $headerHeading = '',
		$headerText = '') {

		$this->_pdf->SetHeaderData($headerLogo, 15, $headerHeading,
			$headerText, array(0,0,0), array(0,0,0));
		$this->_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '',
			PDF_FONT_SIZE_MAIN));
		$this->_pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	}

	/**
	 * Sets the details of the footer
	 *
	 * @return void
	 */
	protected function footerDetailsSet() {

		$this->_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '',
			PDF_FONT_SIZE_DATA));
		$this->_pdf->setFooterData(array(0,0,0), array(0,0,0));
		$this->_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	}

	/**
	 * Sets the details of the Page and its main-body
	 *
	 * @return void
	 */
	protected function pageDetailsSet() {

		$this->_pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP,
			PDF_MARGIN_RIGHT);
		$this->_pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$this->_pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$this->_pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->_pdf->setFontSubsetting(true);
		$this->_pdf->SetFont('helvetica', '', 10, '', true);
		$this->_pdf->setTextShadow($this->_textShadowStyle);
	}

	/**
	 * Writes the content into the main-Body of the PDF
	 *
	 * @return void
	 */
	protected function contentPrint($pageTitle,$pageText) {

		$content = sprintf($this->_contentStr, $pageTitle, $pageText);

		$this->_pdf->writeHTMLCell(0, 0, '', '', $content, 0, 1, 0, true, '',
			true);

		if($this->shouldBarcodePrint()) {
			$this->barcodePrint();
		}
		$this->_pdf->Ln();
	}

	/**
	 * Prints the Barcode to the top of the Header
	 *
	 * @return void
	 */
	protected function barcodePrint() {

		$barcodeCode = $this->barcodeCodeCreate();
		$this->_pdf->write1DBarcode($barcodeCode, $this->_barcodeType, 150, 5,
			'', 15, 0.4, $this->_barcodeStyle, 'N');
	}

	/**
	 * Creates the Code printed as a barcode
	 *
	 * @return string the code
	 */
	protected function barcodeCodeCreate() {

		return sprintf('%s %s',$this->_uid,$this->_loanChoice);
	}

	/**
	 * Checks if the Barcode should be printed onto the Pdf
	 *
	 * @return true if it should be printed, false if not
	 */
	protected function shouldBarcodePrint() {

		return $this->_msgReturn;
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * define barcode style
	 *
	 * @var array
	 */
	protected $_barcodeStyle = array(
		'position' => '',
		'align' => 'C',
		'stretch' => false,
		'fitwidth' => true,
		'cellfitalign' => '',
		'border' => true,
		'hpadding' => 'auto',
		'vpadding' => 'auto',
		'fgcolor' => array(0,0,0),
		'bgcolor' => false, //array(255,255,255),
		'text' => true,
		'font' => 'helvetica',
		'fontsize' => 8,
		'stretchtext' => 4
		);

	/**
	 * Defines the standard text-Shadow-Style
	 *
	 * @var array
	 */
	protected $_textShadowStyle = array(
		'enabled'=>true,
		'depth_w'=>0.2,
		'depth_h'=>0.2,
		'color'=>array(196,196,196),
		'opacity'=>1,
		'blend_mode'=>'Normal'
		);

	/**
	 * The Structure of the Main-Body of the PDF
	 *
	 * @var string
	 */
	protected $_contentStr = '
			<p align="center">
				<h2>
					%s
				</h2>
			</p>
			<br />
				%s
			<br />';

	/**
	 * The TCPDF-Object used by this class
	 *
	 * @var TCPDF
	 */
	protected $_pdf;

	protected $_page1Title;
	protected $_page1Text;
	protected $_page2Title;
	protected $_page2Text;
	protected $_page3Title;
	protected $_page3Text;
	protected $_gradeLevel;
	protected $_msgReturn;
	protected $_loanChoice;
	protected $_uid;

	protected $_barcodeType = 'C128';
}


?>