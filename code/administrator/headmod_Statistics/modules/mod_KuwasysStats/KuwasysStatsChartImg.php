<?php

require_once PCHART_PATH . '/pDraw.class.php';
require_once PCHART_PATH . '/pData.class.php';
require_once PCHART_PATH . '/pImage.class.php';

class KuwasysStatsChartImg {

	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public static function init($interface) {

		self::$_interface = $interface;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	public static function execute() {

		self::dataFetch();
		self::gradelevelCalc();
		self::imageDraw();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches the data needed to create the statistic-image from the server
	 *
	 * @return void
	 */
	protected static function dataFetch() {

		try {
			self::$_chosenClasses = TableMng::query(
				'SELECT g.ID AS ID, g.gradeValue AS value,
					g.label AS label, uic.userCount AS userCount,
					uig.userId AS userId
				FROM grade g
					JOIN jointUsersInGrade uig ON g.ID = uig.gradeId
					JOIN (
						SELECT ID, userId, Count(*) AS userCount, statusId
						FROM jointUsersInClass

						GROUP BY userId
					)
					uic ON uig.userId = uic.userId
				WHERE uic.statusId = (
					SELECT ID FROM usersInClassStatus WHERE name="active"
					) OR uic.statusId = (
					SELECT ID FROM usersInClassStatus WHERE name="waiting"
					)
				', true);

		} catch (MySQLVoidDataException $e) {
			self::$_interface->dieError(
				'Es konnten keine Daten gefunden werden');

		} catch (Exception $e) {
			self::$_interface->dieError(
				'Konnte die nötigen Daten nicht abrufen');
		}
	}

	/**
	 * Creates an Array
	 * @return [type]       [description]
	 */
	protected static function gradelevelCalc() {

		$gradelevel = array();

		foreach(self::$_chosenClasses as $grade) {
			if(!isset($gradelevel[$grade['value']][$grade['label']])) {
				$gradelevel[$grade['value']][$grade['label']] = $grade['userCount'];
			}
			else {
				$gradelevel[$grade['value']][$grade['label']] += $grade['userCount'];
			}
		}

		self::$_gradelevels = $gradelevel;
	}

	protected static function imageDraw() {

		require_once sprintf('%s/StatisticsUseExistingPDataBarChart.php',
			PATH_STATISTICS_CHART);

		$gradeCharts = self::imgGradeBarChartsCreate();

		header('Content-type: image/png');
		imagepng($gradeCharts);
	}

	protected static function imgGradeBarChartsCreate() {

		$newImgHeight = self::$_imgHeight * 3;
		$newImgWidth = self::$_imgWidth;
		$newImg = imagecreatetruecolor($newImgWidth, $newImgHeight);

		//grades
		$data = self::imageGradesDataCreate();
		$chartCreator = new StatisticsUseExistingPDataBarChart($data);
		$chartCreator->setImageWidth(self::$_imgWidth);
		$chartCreator->setImageHeight(self::$_imgHeight);
		$heading = $chartCreator->getHeading();
		$heading['text'] = 'von Klassen gewählte Kurse';
		$chartCreator->setHeading($heading);
		$chartCreator->imageDraw();
		$path = $chartCreator->imageCacheToFile();
		$img = imagecreatefrompng($path);
		imagecopyresampled($newImg, $img, 0, self::$_imgHeight * 0, 0,
			0, self::$_imgWidth, self::$_imgHeight, self::$_imgWidth,
			self::$_imgHeight);
		//gradelevels
		$data = self::imageGradelevelsDataCreate();
		$chartCreator = new StatisticsUseExistingPDataBarChart($data);
		$chartCreator->setImageWidth(self::$_imgWidth);
		$chartCreator->setImageHeight(self::$_imgHeight);
		$heading = $chartCreator->getHeading();
		$heading['text'] = 'von Jahrgängen gewählte Kurse';
		$chartCreator->setHeading($heading);
		$chartCreator->imageDraw();
		$path = $chartCreator->imageCacheToFile();
		$img = imagecreatefrompng($path);
		imagecopyresampled($newImg, $img, 0, self::$_imgHeight * 1, 0,
			0, self::$_imgWidth, self::$_imgHeight, self::$_imgWidth,
			self::$_imgHeight);

		//choicesAdded
		$data = self::imageAllClassUsersDataCreate();
		$chartCreator = new StatisticsUseExistingPDataBarChart($data);
		$heading = $chartCreator->getHeading();
		$heading['text'] = 'Anzahl Wahlen';
		$chartCreator->setHeading($heading);
		$chartCreator->setImageWidth(self::$_imgWidth);
		$chartCreator->setImageHeight(self::$_imgHeight);
		$chartCreator->setMarginRatio(array('X' => 0.15, 'Y' => 0.15));
		$chartCreator->setScale(array("GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE,
			"Pos"=>SCALE_POS_TOPBOTTOM, 'LabelRotation' => 45));
		$chartCreator->imageDraw();
		$path = $chartCreator->imageCacheToFile();
		$img = imagecreatefrompng($path);
		imagecopyresampled($newImg, $img, 0, self::$_imgHeight * 2, 0,
			0, self::$_imgWidth, self::$_imgHeight, self::$_imgWidth,
			self::$_imgHeight);

		return $newImg;
	}

	/**
	 * Creates and returns a pData-Object which is used to display a graph
	 *
	 * @return pData
	 */
	protected static function imageGradesDataCreate() {

		//init the data-Input
		$dataArray = array();
		$dataNameArray = array();

		foreach(self::$_gradelevels as $gradelevel => $grades) {
			foreach($grades as $gradeLabel => $grade) {
				$name = sprintf("%s-%s", $gradelevel, $gradeLabel);
				$dataArray[] = $grade;
				$dataNameArray[] = $name;
			}
		}

		//Set the data
		$imgData = new pData();
		$imgData->addPoints($dataArray, "gewählte Kurse");
		$imgData->addPoints($dataNameArray, "gradenames");
		// $imgData->setSerieTicks("gewählte Kurse");
		$imgData->setSerieDescription('gradenames', 'Klasse');
		$imgData->setAbscissa('gradenames');
		$imgData->setAxisName(0,"gewählte Kurse");

		return $imgData;
	}

	/**
	 * Creates and returns a pData-Boject containing information how the
	 * BarChart of gradelevels looks like
	 *
	 * @return void
	 */
	protected static function imageGradelevelsDataCreate() {

		$dataArray = array();
		$dataNameArray = array();

		foreach(self::$_gradelevels as $gradelevel => $grades) {
			$choiceCount = 0;
			foreach($grades as $grade) {
				$choiceCount += $grade;
			}
			$dataArray[] = $choiceCount;
			$dataNameArray[] = $gradelevel;
		}

		//Set the data
		$imgData = new pData();
		$imgData->addPoints($dataArray, "classesChosen");
		$imgData->addPoints($dataNameArray, "gradenames");
		$imgData->setPalette('classesChosen', array("R"=>224,"G"=>214,"B"=>46));
		// $imgData->setSerieTicks("gewählte Kurse");
		$imgData->setSerieDescription('gradenames', 'Jahrgang');
		$imgData->setAbscissa('gradenames');
		$imgData->setAxisName(0,"gewählte Kurse");

		return $imgData;
	}

	/**
	 * @todo FINISH HIM!!!
	 * @return [type] [description]
	 */
	protected static function imageTypeOfSchoolsCreate() {


	}

	protected static function imageAllClassUsersDataCreate() {

		$dataArray = array();
		$dataNameArray = array();

		$choiceCount = 0;
		foreach(self::$_gradelevels as $gradelevel => $grades) {
			foreach($grades as $grade) {
				$choiceCount += $grade;
			}
		}
		$choiceCount;

		$uniqueUsers = array();
		foreach(self::$_chosenClasses as $classSelection) {
			$uniqueUsers[$classSelection['userId']] = true;
		}
		$uniqueUsersCount = count($uniqueUsers);

		//Set the data
		$imgData = new pData();
		$imgData->addPoints(array($choiceCount, $uniqueUsersCount),
			"chosenClasses");
		$imgData->addPoints(array('Kurswahlen', 'Schüler'), 'descr');
		$imgData->setPalette('chosenClasses', array("R"=>224,"G"=>100,"B"=>46));
		// $imgData->addPoints($dataNameArray, 'gradenames');
		// $imgData->setSerieTicks('gewählte Kurse');
		$imgData->setSerieDescription('descr', 'test');
		$imgData->setAbscissa('descr');
		$imgData->setAxisName(0,'gewählte Kurse');
		return $imgData;

	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	protected static $_interface;

	protected static $_gradelevels;

	protected static $_chosenClasses;

	protected static $_imgWidth = 800;
	protected static $_imgHeight = 300;


}

?>