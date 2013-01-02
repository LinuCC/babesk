<?php

/**
 * This Class allows combining multiple Pdfs to one Pdf using TCPDF
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 */
class joinMultiplePdf {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct () {

		require_once PATH_INCLUDE . '/pdf/tcpdf/tcpdf.php';
		require_once PATH_INCLUDE . '/pdf/fpdi/fpdi.php';

		$this->_fpdi = new FPDI();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * Adds a PDF-filePath to an Array, so it can be combined later on.
	 * @param string $pdfStr The PDF
	 * @throws InvalidArgumentException if $pdfStr is not a string
	 */
	public function pdfAdd ($pdfStr) {

		if(!is_string($pdfStr)) {
			throw new InvalidArgumentException('The PDF-file is not a string.');
		}
		$this->_pdfArray [] = $pdfStr;
	}

	public function pdfCombine () {

		/**
		 * Code used from http://neo22s.com/concatenate-pdf-in-php/
		 * Thanks to the Author ("Chena") for this useful script!
		 */
		foreach ($this->_pdfArray as $pdfPage) {
			$pagecount = $this->_fpdi->setSourceFile($pdfPage);

			for ($i = 1; $i <= $pagecount; $i++) {
				$tplidx = $this->_fpdi->ImportPage($i);
				$s = $this->_fpdi->getTemplatesize($tplidx);
				$this->_fpdi->AddPage('P', array($s['w'], $s['h']));
				$this->_fpdi->useTemplate($tplidx);
			}
		}
		$this->_combinedPdf = $this->_fpdi->Output('', 'S');
	}

	/**
	 * Returns the PDF-String of the combined PDF
	 */
	public function pdfCombinedGet () {

		return $this->_combinedPdf;
	}

	/**
	 * Provides a download of the Pdf in the userBrowser
	 */
	public function pdfCombinedProvideAsDownload ($pdfFileName = "pdfPage.pdf") {
		$this->_fpdi->Output($pdfFileName, 'I');
	}

	/**
	 * delete the content of the temporary Dir of the PDF-files.
	 * The PDF's are generated dynamically, we need to free space up.
	 */
	public function pdfTempDirClean () {

		$this->rmTmpPdf();
	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * Removes all Temporary saved PDF's
	 */
	private function rmTmpPdf() {

		///@todo this is also in HtmlToPdfImporter, refactoring needed
		$tempDirPath = PATH_INCLUDE . '/pdf/tempPdf/';
		$dirToSearchArr = array('/tmp', $tempDirPath);

		foreach ($dirToSearchArr as $dir) {
			foreach(glob($dir . '/kuwasysPdf*') as $file) {
				if(is_dir($file)) {
					echo 'A temp folder with "kuwasysPdf" in it was found?!';
				}
				else {
					unlink($file);
				}
			}
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * The array of Pdfs that will be combined
	 * @var array[pdfpaths]
	 */
	protected $_pdfArray;

	/**
	 * The combined pdfstring
	 * @var string
	 */
	protected $_combinedPdf;

	/**
	 * A tcpdf-Object, fpdi needs it
	 * @var TCPDF
	 */
	protected $_tcpdf;

	/**
	 * An fpdi-Object, will contain the Data for the combined PDF
	 * @var FPDI
	 */
	protected $_fpdi;
}

?>