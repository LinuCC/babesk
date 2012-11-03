<?php

require_once PATH_INCLUDE . '/Module.php';

class Pvp extends Module {

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
		
		global $smarty;
		require_once PATH_ACCESS .'/PVauManager.php';
		$this->pvm = new PVauManager();
		
		include('simple_html_dom.php');		
		if (isset($_POST['search']) && $_POST['search'] != "" && $_POST['search'] != " ") {
			$this->pvm->SetSearchterms($_SESSION['uid'], $_POST['search']);
		}
		
		
		// get DOM from URL
		$today = file_get_html('http://www.leg-uelzen.de/vertretungsplan/Heute/subst_001.htm');
		$tomorrow = file_get_html('http://www.leg-uelzen.de/vertretungsplan/Morgen/subst_001.htm');
		$searchterm = $this->pvm->getSearchterms($_SESSION['uid']);
		
		$smarty->assign('searchterm',$searchterm);
		if (isset($searchterm))  {
			$smarty->assign('planheute',$this->createPVP($today,$searchterm));
			$smarty->assign('planmorgen',$this->createPVP($tomorrow,$searchterm));
		} else {
			$smarty->assign('planheute','<p class="error">Keine Suchbegriffe angegeben!</p>');
		}
		$smarty->display($this->smartyPath . "pvp.tpl");	
	}
	
	private function createPVP($date,$searchterm) {
		
		$searchterm_exploded = explode(" ", $searchterm);
		// remove all meta
		foreach($date->find('meta') as $e)
			$e->outertext = '';

		$treffer = 0;
		$result_tmp='';
		// replace all input
		foreach($date->find('div.mon_title') as $e)
			$result = '<h3>'.$e->outertext . '</h3>'; 
		$result .= "<table>";
		$th = $date->find('tr.list');
		
			$result .= '<h3>'.$th[0]->outertext . '</h3>';
			foreach ($searchterm_exploded as $st) {
				foreach($date->find('tr') as $e) {
			
				if (strstr($e->innertext,sprintf('>%s</td>',$st))) {	
					$treffer = 1;
					$result_tmp .= '<tr>'.$e->innertext().'</tr>';
					$result_tmp = str_get_html($result_tmp);
					foreach($result_tmp->find('td') as $e) {
						if ($e->innertext == '&nbsp') $e->outertext='';
							$e->style  ='';
					}
				} else
					$e->outertext = '';
				}
			}
				
	
	
	if (!$treffer) {
		foreach($date->find('div.mon_title') as $e)
			$result = '<h3>'.$e->outertext . '</h3>';
		$result .= "<h4>Keine Vertretungen!</h4>";
	}
	else $result .= $result_tmp; 
		// dump contents
		$result .= "</table>";
		return $result;
	}
}
?>