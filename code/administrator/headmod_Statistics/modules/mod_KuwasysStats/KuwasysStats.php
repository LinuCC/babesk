<?php

require_once PATH_INCLUDE . '/Module.php';

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

					// $this->test2();
		if(isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'imgCountOfChosenClassesPerGrade':
					$this->imageCountOfChosenClassesPerGradeCreate();
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

		require_once 'KuwasysStatsChartImg.php';
		KuwasysStatsChartImg::init($this->_interface);
		KuwasysStatsChartImg::execute();
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

	protected function test2() {
		/* pChart library inclusions */
		require_once PCHART_PATH . '/pDraw.class.php';
		require_once PCHART_PATH . '/pData.class.php';
		require_once PCHART_PATH . '/pImage.class.php';


		/* Create and populate the pData object */
		$MyData = new pData();
		$MyData->addPoints(array(-4,VOID,VOID,12,8,3),"Probe 1");
		$MyData->addPoints(array(3,12,15,8,5,-5),"Probe 2");
		$MyData->addPoints(array(2,0,5,18,19,22),"Probe 3");
		$MyData->setSerieTicks("Probe 2",4);
		$MyData->setAxisName(0,"Temperatures");
		$MyData->addPoints(array("Jan","Feb","Mar","Apr","May","Jun"),"Labels");
		$MyData->setSerieDescription("Labels","Months");
		$MyData->setAbscissa("Labels");

		/* Create the pChart object */
		$myPicture = new pImage(700,230,$MyData);

		/* Draw the background */
		$Settings = array("R"=>170, "G"=>183, "B"=>87, "Dash"=>1, "DashR"=>190, "DashG"=>203, "DashB"=>107);
		$myPicture->drawFilledRectangle(0,0,700,230,$Settings);

		/* Overlay with a gradient */
		$Settings = array("StartR"=>219, "StartG"=>231, "StartB"=>139, "EndR"=>1, "EndG"=>138, "EndB"=>68, "Alpha"=>50);
		$myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,$Settings);
		$myPicture->drawGradientArea(0,0,700,20,DIRECTION_VERTICAL,array("StartR"=>0,"StartG"=>0,"StartB"=>0,"EndR"=>50,"EndG"=>50,"EndB"=>50,"Alpha"=>80));

		/* Add a border to the picture */
		$myPicture->drawRectangle(0,0,699,229,array("R"=>0,"G"=>0,"B"=>0));

		/* Write the picture title */
		$myPicture->setFontProperties(array("FontName"=>"../fonts/Silkscreen.ttf","FontSize"=>6));
		$myPicture->drawText(10,13,"drawBarChart() - draw a bar chart",array("R"=>255,"G"=>255,"B"=>255));


		$myPicture->setFontProperties(array("FontName"=>PATH_INCLUDE . '/fonts/GeosansLight.ttf',"FontSize"=>10));
		/* Write the chart title */
		$myPicture->setFontProperties(array("FontName"=>PATH_INCLUDE . "/fonts/Forgotte.ttf","FontSize"=>11));
		$myPicture->drawText(250,55,"Average temperature",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

		/* Draw the scale and the 1st chart */
		$myPicture->setGraphArea(60,60,450,190);
		$myPicture->drawFilledRectangle(60,60,450,190,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
		$myPicture->drawScale(array("DrawSubTicks"=>TRUE));
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$myPicture->setFontProperties(array("FontName"=>PATH_INCLUDE . "/fonts/pf_arma_five.ttf","FontSize"=>6));
		$myPicture->drawBarChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO,"Rounded"=>TRUE,"Surrounding"=>30));
		$myPicture->setShadow(FALSE);

		/* Draw the scale and the 2nd chart */
		$myPicture->setGraphArea(500,60,670,190);
		$myPicture->drawFilledRectangle(500,60,670,190,array("R"=>255,"G"=>255,"B"=>255,"Surrounding"=>-200,"Alpha"=>10));
		$myPicture->drawScale(array("Pos"=>SCALE_POS_TOPBOTTOM,"DrawSubTicks"=>TRUE));
		$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
		$myPicture->drawBarChart();
		$myPicture->setShadow(FALSE);

		/* Write the chart legend */
		$myPicture->drawLegend(510,205,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

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