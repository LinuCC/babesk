<?php

require_once PATH_INCLUDE . '/Module.php';

define('PCHART_PATH', PATH_INCLUDE . '/pChart');

/**
 * Analyzes data of the headmodule Kuwasys and puts them out as statistics
 *
 * @author  Pascal Ernst <pascal.cc.ernst@gmail.com>
 */
class KuwasysStats extends Module {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct($name, $display_name, $path) {

		parent::__construct($name, $display_name, $path);
		$this->_smartyPath = PATH_SMARTY . '/templates/administrator/' . $path;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public function execute($dataContainer) {

		$this->entryPoint($dataContainer);
		$this->imageCountOfChosenClassesPerGradeCreate();

		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'imgCountOfChosenClassesPerGrade':
					// $this->test();
					break;
				default:
					die('Wrong action-value given');
			}
		}
		else {
			$this->mainPage();
		}
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	protected function entryPoint($dataContainer) {

		defined('_AEXEC') or die("Access denied");

		$this->_smarty = $dataContainer->getSmarty();
		$this->_interface = $dataContainer->getInterface();
	}

	protected function mainPage() {

		$this->_smarty->display($this->_smartyPath . 'main.tpl');
	}

	protected function imageCountOfChosenClassesPerGradeCreate() {

		require_once 'KuwasysStatsImgCountOfChosenClasses.php';
		KuwasysStatsImgCountOfChosenClasses::init($this->_interface);
		KuwasysStatsImgCountOfChosenClasses::execute();
	}


	protected function test() {

		require_once PCHART_PATH . '/pDraw.class.php';
		require_once PCHART_PATH . '/pData.class.php';
		require_once PCHART_PATH . '/pImage.class.php';

		/* Create and populate the pData object */
		$MyData = new pData();
		$MyData->addPoints(array(150,220,300,250,420,200,300,200,100),"Server A");
		$MyData->addPoints(array(140,0,340,300,320,300,200,100,50),"Server B");
		$MyData->setAxisName(0,"Hits");
		$MyData->addPoints(array("January","February","March","April","May","Juin","July","August","September"),"Months");
		$MyData->setSerieDescription("Months","Month");
		$MyData->setAbscissa("Months");

		/* Create the pChart object */
		$myPicture = new pImage(700,230,$MyData);

		/* Turn of Antialiasing */
		$myPicture->Antialias = FALSE;

		/* Add a border to the picture */
		$myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
		$myPicture->drawGradientArea(0,0,700,230,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
		$myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

		/* Set the default font */
		$myPicture->setFontProperties(array("FontName"=>PATH_INCLUDE . '/fonts/GeosansLight.ttf',"FontSize"=>10));

		/* Define the chart area */
		$myPicture->setGraphArea(60,40,650,200);

		/* Draw the scale */
		$scaleSettings = array("GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
		$myPicture->drawScale($scaleSettings);

		/* Write the chart legend */
		$myPicture->drawLegend(580,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

		/* Turn on shadow computing */
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));

		/* Draw the chart */
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$settings = array("Surrounding"=>-30,"InnerSurrounding"=>30,"Interleave"=>0);
		$myPicture->drawBarChart($settings);

		/* Render the picture (choose the best way) */
		$myPicture->autoOutput("pictures/example.drawBarChart.spacing.png");
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected $_smarty;
	protected $_interface;

}

?>