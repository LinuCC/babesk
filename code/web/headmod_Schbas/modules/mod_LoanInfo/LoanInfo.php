<?php

require_once PATH_INCLUDE . '/Module.php';
require_once PATH_WEB . '/WebInterface.php';

class LoanInfo extends Module {

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
		
		var_dump($_POST);
		global $smarty;
		require_once PATH_ACCESS. '/BookManager.php';
		require_once PATH_INCLUDE . '/TableMng.php';
		TableMng::init ();
		$booklistManager = new BookManager();
		
		//get gradeValue ("Klassenstufe")
		$gradeValue = TableMng::query("SELECT gradeValue FROM grade WHERE id=(SELECT GradeID from jointusersingrade WHERE UserID='".$_SESSION['uid']."')",true);
		$smarty->assign('gradeValue', $gradeValue[0]['gradeValue']);
		// get cover letter ("Anschreiben")
		$coverLetter = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='coverLetter'",true);
		$smarty->assign('coverLetterTitle', $coverLetter[0]['title']);
		$smarty->assign('coverLetterText', $coverLetter[0]['text']);
		
		// get first infotext
		$textOne = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textOne".$gradeValue[0]['gradeValue']."'",true);
		$smarty->assign('textOneTitle', $textOne[0]['title']);
		$smarty->assign('textOneText', $textOne[0]['text']);
		
		// get second infotext
		$textTwo = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textTwo".$gradeValue[0]['gradeValue']."'",true);
		$smarty->assign('textTwoTitle', $textTwo[0]['title']);
		$smarty->assign('textTwoText', $textTwo[0]['text']);
		
		// get third infotext
		$textThree = TableMng::query("SELECT title, text FROM schbas_texts WHERE description='textThree".$gradeValue[0]['gradeValue']."'",true);
		$smarty->assign('textThreeTitle', $textThree[0]['title']);
		$smarty->assign('textThreeText', $textThree[0]['text']);
		
		// get booklist
		$booklist = $booklistManager->getBooksByClass($gradeValue[0]['gradeValue']);
		$smarty->assign('booklist', $booklist);
		$smarty->display($this->smartyPath . "loanInfo.tpl");
	}
}
?>