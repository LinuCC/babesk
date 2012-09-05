<?php

require_once PATH_INCLUDE . '/pdf/tcpdf/tcpdf.php';
/**
 * This class allows importing a Html-file / string, replacing TemplateVariableNames surrounded by a specific pattern
 * with Strings, and converting the Html-code to a PDF-file. Additionally, this class allows importing XML-Files that
 * can define Configuration and Variables for the PDF-file, as well as the path to the Html-file itself, for easier
 * Initialization.
 * It needs to be able to make directories and files in the same folder, so make sure you have the right permissions
 * set!
 * @todo create the functions to import Xml's
 * @todo create example-xmls to show off the use of importing XML's
 * @author Pascal Ernst <pascal.cc.ernst@googlemail.com>
 *
 */
class HtmlToPdfImporter extends TCPDF {

	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
		/**
	 * @param string $pageOrientation the pageOrientation, like "Portrait" or "Landscape". Standard is Protrait.
	 * @param string $unit the measurement of the pdf-Site, like "cm", "mm", "pt" or "in". Standard: cm.
	 * @param string $pageFormat the Format of the Page, like "A4". Standard: A4.
	 */
	public function __construct ($pageOrientation = "Portrait", $unit = "cm", $pageFormat = "A4") {


		parent::__construct($pageOrientation, $unit, $pageFormat);
		$this->_htmlCode = '';
		$this->_tempVarSurroundPattern = '';
		$this->_tempDirName = 'kuwasysPdf';
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * Sets the surroundingPattern-String to the Parameter. It defines for what the Class should search when
	 * trying to replace Temporary Variables in the Html-File. The "%s" in the string defines the Position of the
	 * Variable-name.
	 * An Example would be '#!%s#!' when the Temporary Variable looks like this: '#!TempVarName#!'
	 * @param string $str the SurroundPattern-String
	 */
	public function tempVarSurroundingPatternSet ($str) {
	
		$this->_tempVarSurroundPattern = $str;
	}
	
	/**
	 * returns the Pdf as a string
	 * @return string The PDF-String
	 */
	public function pdfGet () {
		
		return $this->Output('', 'S');
	}

	/**
	 * Saves the PDF as a PDF-file and returns the Path
	 * to the saved File
	 * @return string The Path to the temporary PDF-file
	 */
	public function pdfSaveTemporaryAndGetFilename () {

		$tempDirPath = PATH_INCLUDE . '/pdf/tempPdf/';
		$filePrefix = 'kuwasysPdf';

		if(!is_dir($tempDirPath)) {
			mkdir($tempDirPath, '0777');
		}
		$tmpFilePath = tempnam($tempDirPath, $filePrefix);
		if(!$tmpFilePath) {
			throw new Exception('Could not create the Temporary File! Check permissions of ' . $tempDirPath);
		}
		$this->fileWriteInto($tmpFilePath, $this->Output('', 'S'));

		return $tmpFilePath;
	}

	/**
	 * Provides a download of the Pdf in the userBrowser
	 */
	public function pdfProvideAsDownload () {

		$this->Output('schinken.pdf', 'I');
	}
	
	/**
	 * This function imports the Html-Code into the Class, to use it later on.
	 * @param string $htmlString The Path to the HTML-File or the html-string
	 * @param booloean $isFile If set to true, the function will interpret $htmlString as a path To a file. Otherwise
	 * it will treat $htmlString as the Html-String itself.
	 */
	public function htmlImport($htmlString, $isFile) {

		if($isFile) {
			$this->_htmlCode = $this->loadHtmlFile($htmlString);
		}
		else {
			$this->_htmlCode = $htmlString;
		}
	}
	
	/**
	 * This function replaces a Temporary Variable in the HtmlCode with a Value.
	 * @param string $varName The Name of the Temporary Variable to search for
	 * @param string $replaceValue The string with which the Temporary Variable gets replaced
	 * @param string $surroundPattern The Surrounding-Pattern of the Temporary Variable. Optional. If left out,
	 * the function will use the string which was previously given to the tempVarSurroundingPatternSet() function
	 * @throws InvalidArgumentException If surroundpattern was left out but tempVarSurroundingPatternSet() was not
	 * called previously
	 */
	public function tempVarReplaceInHtml ($varName, $replaceValue, $surroundPattern = false) {
		
		if(!$surroundPattern) {
			if($this->_tempVarSurroundPattern == '') {
				throw new InvalidArgumentException('Could not define surroundPattern; call tempVarSurroundingPatternSet() first!');
			}
			$searchString = sprintf($this->_tempVarSurroundPattern, $varName); // the string to search for
		}
		else {
			$searchString = sprintf($surroundPattern, $varName); // the string to search for	
		}
		//check if String is existing
		if(strpos($this->_htmlCode, $searchString) === false) {
			throw new Exception (sprintf('Failed to find the Temporary Variable %s!', $searchString));
		}
		$this->_htmlCode = str_replace($searchString, $replaceValue, $this->_htmlCode);
	}
	
	/**
	 * Converts the Html-Code into a PDF-file.
	 */
	public function htmlToPdfConvert () {
		
		$this->AddPage();
		$this->writeHTML($this->_htmlCode);
	}
	
	/**
	 * This method acts as a link to the tcpdf-class. You can call a function of the tcpdf-class to change and add 
	 * data to the Pdf-file, like the author, or setting the font. Use it like call_user_func().
	 * Example: 'tcpdfCommandUse("SetAuthor", "Pascal Ernst")', and the Pdf-File will be changed based on that.
	 * @param string $functionCall the function to call
	 */
	public function tcpdfCommandUse ($functionCall) {
		
		$funcParameter = func_get_args();
		unset($funcParameter [0]);//first Parameter is functionname, we only want the parameter as an array
		call_user_func_array(array($this, $functionCall), $funcParameter);
	}

	/**
	 * Returns the Count of Sites of the Pdf-File
	 */
	public function getCountOfPdfSites () {
		
		return $this->getNumPages();
	}
	
	
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * loads a file and returns its content
	 * @param string $htmlFilePath the Path to the Html-File
	 * @return string $htmlCode the extracted content of the Html-File
	 */
	private function loadHtmlFile ($htmlFilePath) {

		if (file_exists($htmlFilePath)) {
			$htmlCode = file_get_contents($htmlFilePath);
		}
		//search at other places for this file
		else if (file_exists(PATH_CODE . '/' . $htmlFilePath)) {
			$htmlCode = file_get_contents(PATH_CODE . '/' . $htmlFilePath);
		}
		else {
			throw new InvalidArgumentException(sprintf('Could not find the Htmlfile. Path: %s', $htmlFilePath));
		}
		return $htmlCode;
	}

	private function fileWriteInto ($filePath, $string) {

		$handle = fopen ($filePath, 'w');
		if(!$handle) {
			throw new Exception(sprintf('Could not write file at %s', $filePath));
		}
		fwrite($handle, $string);
		fclose($handle);
	}
	
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////
	/**
	* The Html-Code
	* @var string
	*/
	private $_htmlCode;

	/**
	 * A string that surrounds the TemplateVariables in the Html-file to make them replacable.
	 * Should be used with sprintf
	 * @var string
	 */
	private $_tempVarSurroundPattern;
	
	private $_tempDirName;
}

?>