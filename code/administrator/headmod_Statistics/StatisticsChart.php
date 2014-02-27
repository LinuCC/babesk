<?php

require_once PATH_INCLUDE . '/TemporaryFile.php';

/**
 * Base-Class, allowing creation of Charts using pChart
 *
 * ======= HowTo: Create a Chart-Drawer ========
 *
 * The Statistics-Chart-Classes should be used like this: StatisticsChart is the
 * Parent-Class, every class should inherit from it. The class inheriting from
 * this class should only specify which kind of Chart to draw (see
 * StatisticsBarChart as an Example). Create a child-class extending from that
 * parentClass. This draws the Image. Overview:
 * [StatisticsChart > Statistics{kindOfChart}Chart > Class drawing the chart]
 *
 * Put your data-Fetching into the dataFetch()-routine and save the raw data
 * into _externalData. dataProcess() makes a pData-Object (saved in _pData)
 * from the raw data in _externalData. You can overwrite any
 * function-definition you want to change the way the Chart gets drawn.
 */
abstract class StatisticsChart {
	/////////////////////////////////////////////////////////////////////
	//Constructor
	/////////////////////////////////////////////////////////////////////

	public function __construct() {

		require_once PCHART_PATH . '/pDraw.class.php';
		require_once PCHART_PATH . '/pData.class.php';
		require_once PCHART_PATH . '/pImage.class.php';

	}

	/////////////////////////////////////////////////////////////////////
	//Getters Setters
	/////////////////////////////////////////////////////////////////////

	/**
	 * Returns the Image-Container
	 *
	 * @return pImage the pImage used by pChart
	 */
	public function getPImage() {
		return $this->_pImage;
	}

	/**
	 * Sets the ImageContainer
	 *
	 * @param PImage $pImage The pImage used by pChart
	 */
	public function setPImage($pImage) {
		$this->_pImage = $pImage;
		return $this;
	}


	/**
	 * Returns the DataContainer
	 *
	 * @return pData the pData used by pChart to create the Image
	 */
	public function getPData() {
		return $this->_pData;
	}

	/**
	 * Sets the Data-Container used by this class
	 *
	 * @param PData $pData The pData used by pChart to create the Image
	 */
	public function setPData($pData) {
		$this->_pData = $pData;
		return $this;
	}


	/**
	 * Returns the Settings of the Scale of the Chart
	 *
	 * @return array An Array describing the settings of the Scale
	 */
	public function getScale() {
		return $this->_scale;
	}

	/**
	 * Sets the Settings of the Scale of the Chart
	 *
	 * @param array $scale An Array describing the settings of the Scale
	 */
	public function setScale($scale) {
		$this->_scale = $scale;
		return $this;
	}


	/**
	 * Returns the width of the Image
	 *
	 * @return int The Width of the Image in Pixel
	 */
	public function getImageWidth() {
		return $this->_imageWidth;
	}

	/**
	 * Sets the Width of the Image
	 *
	 * @param Int $imageWidth The Width of the Image in Pixel
	 */
	public function setImageWidth($imageWidth) {
		$this->_imageWidth = $imageWidth;
		return $this;
	}


	/**
	 * Returns the Height of the Image
	 *
	 * @return int The Height of the Image in Pixel
	 */
	public function getImageHeight() {
		return $this->_imageHeight;
	}

	/**
	 * Sets the Height of the Image
	 *
	 * @param Int $imageHeight The Height of the Image in Pixel
	 */
	public function setImageHeight($imageHeight) {
		$this->_imageHeight = $imageHeight;
		return $this;
	}


	/**
	 * Returns the Width of the Chart
	 *
	 * @return Int The Width of the Chart
	 */
	public function getChartWidth() {
		return $this->_chartWidth;
	}

	/**
	 * Sets the Width of the Chart
	 *
	 * @param Int $chartWidth The Width of the Chart
	 */
	public function setChartWidth($chartWidth) {
		$this->_chartWidth = $chartWidth;
		return $this;
	}


	/**
	 * Returns the Height of the Chart
	 *
	 * @return Int The Height of the Chart
	 */
	public function getChartHeight() {
		return $this->_chartHeight;
	}

	/**
	 * Sets the Height of the Chart
	 *
	 * @param Int $chartHeight The Height of the Chart
	 */
	public function setChartHeight($chartHeight) {
		$this->_chartHeight = $chartHeight;
		return $this;
	}

	/**
	 * Returns the Margin Ratio, describing how the Chart is positioned in the
	 * image
	 *
	 * @return array An Array containing the elements X and Y
	 */
	public function getMarginRatio() {
		return $this->_marginRatio;
	}

	/**
	 * Sets the marginRatio, describing how the Chart is positioned in the
	 * image. Expected is an array with the keys X and Y, each one with values
	 * in the range of 0 to 1 (percentage)
	 *
	 * @param Array $marginRatio An Array containing the elements X and Y
	 */
	public function setMarginRatio($marginRatio) {
		$this->_marginRatio = $marginRatio;
		return $this;
	}


	/**
	 * Returns the Settings configuring the Heading of the Image
	 *
	 * @return Array The Settings of the Heading
	 */
	public function getHeading() {
		return $this->_heading;
	}

	/**
	 * Sets the Configuration of the Heading of the Image
	 *
	 * @param Array $heading The Settings of the Heading
	 */
	public function setHeading($heading) {
		$this->_heading = $heading;
		return $this;
	}

	/////////////////////////////////////////////////////////////////////
	//Methods
	/////////////////////////////////////////////////////////////////////

	/**
	 * Draws the whole Image and the chart
	 */
	public function imageDraw($externalData = NULL) {

		if(!isset($this->_pData)) { //was setPData() used?
			if(!isset($externalData)) {
				$this->dataFetch();
			}
			else {
				$this->_externalData = $externalData;
			}
		}
		$this->dataProcess();
		$this->imageInit();
		$this->imagePropertiesSet();
		$this->imageBackgroundDraw();
		$this->imageScaleDraw();
		$this->imageLegendDraw();
		$this->imageHeadingDraw();
		$this->imageChartDraw();
	}

	/**
	 * Outputs the drawn Image to the clients Browser
	 *
	 * @return void
	 */
	public function imageDisplay() {

		$this->_pImage->Stroke();
	}

	/////////////////////////////////////////////////////////////////////
	//Implements
	/////////////////////////////////////////////////////////////////////

	/**
	 * Fetches the Data if the external data was not set
	 * @return void
	 */
	abstract protected function dataFetch();

	abstract protected function dataProcess();

	abstract protected function imageChartDraw();

	/**
	 * Initializes the pImage using the pData
	 * @return void
	 */
	protected function imageInit() {

		$this->_pImage = new pImage($this->_imageWidth, $this->_imageHeight,
			$this->_pData);
	}

	/**
	 * Sets the properties of the Image
	 * @throws If Image is smaller or has same size as Chart-size
	 * @return void
	 */
	protected function imagePropertiesSet() {

		$widthDiff = $this->_imageWidth - $this->_chartWidth;
		$heightDiff = $this->_imageHeight - $this->_chartHeight;

		$this->_pImage->Antialias = $this->_antialias;
		$this->_pImage->setShadow(TRUE, $this->_shadow);
		$this->_pImage->setFontProperties(array(
			'FontName' => PATH_INCLUDE . $this->_fontRelativePath,
			'FontSize' => $this->_fontSize));

		if($widthDiff > 0 && $heightDiff > 0) {
			$this->_pImage->setGraphArea(
				(1 - $this->_marginRatio['X']) * $widthDiff,
				(1 - $this->_marginRatio['Y']) * $heightDiff,
				$this->_chartWidth +
					((1 - $this->_marginRatio['X']) * $widthDiff),
				$this->_chartHeight + ((1 - $this->_marginRatio['Y'])
					* $heightDiff));
		}
		else {
			throw new Exception('The Image is smaller or has the same size compared to the Chart-Size! Make it bigger!');
		}
	}

	/**
	 * Draws the Background of the Image
	 * @return void
	 */
	protected function imageBackgroundDraw() {

		$this->_pImage->drawFilledRectangle(0, 0, $this->_imageWidth,
			$this->_imageHeight, $this->_backgroundColor);

		$this->_pImage->drawGradientArea(0, 0, $this->_imageWidth,
			$this->_imageHeight, $this->_backgroundGradientDirection,
			$this->_backgroundGradient);

		$this->_pImage->drawRectangle(0, 0, $this->_imageWidth - 1,
			$this->_imageHeight - 1, $this->_border);
	}

	/**
	 * Draws the Scale of the Chart onto the Image
	 * @return void
	 */
	protected function imageScaleDraw() {

		$this->_pImage->drawScale($this->_scale);
	}

	/**
	 * Draws the Legend of the Chart onto the Image
	 * @return void
	 */
	protected function imageLegendDraw() {

		$this->_pImage->drawLegend($this->_imageWidth -170, 12, $this->_legend);
	}

	protected function imageHeadingDraw() {

		$x = ($this->_heading['X'] == -1) ?
			$this->_imageWidth / 2 : $this->_heading['X'];

		$this->_pImage->drawText($x, $this->_heading['Y'],
			$this->_heading['text'], $this->_heading);
	}

	/////////////////////////////////////////////////////////////////////
	//Attributes
	/////////////////////////////////////////////////////////////////////

	/**
	 * Contains the Image
	 * @var pImage
	 */
	protected $_pImage = NULL;

	/**
	 * Containing the Data for the Image
	 * @var pData
	 */
	protected $_pData = NULL;

	/**
	 * Contains external Data that should be processed to pData
	 * @var ?
	 */
	protected $_externalData;

	/**
	 * Settings defining the Color of the Background
	 * @var array
	 */
	protected $_backgroundColor = array(
		'R' => 170,
		'G' => 183,
		'B' => 87,
		'Dash' => 1,
		'DashR' => 190,
		'DashG' => 203,
		'DashB' => 107
	);

	/**
	 * Settings defining the background-Gradient of the Image
	 * @var array
	 */
	protected $_backgroundGradient = array(
		'StartR' => 219,
		'StartG' => 231,
		'StartB' => 139,
		'EndR' => 1,
		'EndG' => 138,
		'EndB' => 68,
		'Alpha' => 50
	);

	/**
	 * Default drawing direction of the Gradient in the background
	 * @var const
	 */
	protected $_backgroundGradientDirection = DIRECTION_VERTICAL;

	/**
	 * Settings defining the Border surrounding the Image
	 * @var array
	 */
	protected $_border = array(
		'R' => 0,
		'G' => 0,
		'B' => 0
	);

	/**
	 * Settings defining the Shadow of the Chart
	 * @var array
	 */
	protected $_shadow = array(
		'X' => 1,
		'Y' => 1,
		'R' => 0,
		'G' => 0,
		'B' => 0,
		'Alpha' => 10
	);

	/**
	 * Settings defining the Scale of the Chart
	 * @var array
	 */
	protected $_scale = array(
		'GridR' => 200,
		'GridG' => 200,
		'GridB' => 200,
		'DrawSubTicks' => TRUE,
		// 'CycleBackground' => FALSE,
		'LabelRotation'  =>  45
	);

	/**
	 * Settings defining how the legend should be drawn onto the image
	 * @var array
	 */
	protected $_legend = array(
		'Style' => LEGEND_NOBORDER,
		'Mode' => LEGEND_VERTICAL
	);

	/**
	 * Styles the Heading of the Image
	 * When X=-1, the Heading gets aligned to the middle
	 * @var array
	 */
	protected $_heading = array(
		'FontSize' => 20,
		'Align' => TEXT_ALIGN_BOTTOMMIDDLE,
		'X' => -1,
		'Y' => 45,
		'text' => ''
	);

	/**
	 * A color-palette, allowing a different color for graphs
	 * @var array
	 */
	protected $_palette = array(
		"0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>100),
		"1"=>array("R"=>150,"G"=>200,"B"=>46,"Alpha"=>100),
	);

	/**
	 * The Width of the created Image
	 * @var integer
	 */
	protected $_imageWidth = 1000;

	/**
	 * The Height of the created Image
	 * @var integer
	 */
	protected $_imageHeight = 300;

	/**
	 * The Width of the Chart in the Image
	 * @var integer
	 */
	protected $_chartWidth = 900;

	/**
	 * The Height of the Chart in the Image
	 * @var integer
	 */
	protected $_chartHeight = 200;

	/**
	 * Sets how the Chart should be positioned in the Image;
	 * value between 0 and 1
	 * @var array
	 */
	protected $_marginRatio = array(
		'X' => 0.4,
		'Y' => 0.4);

	/**
	 * The relative Path to the font-file of the font used in the Charts
	 * @var string
	 * @note Could not use the PATH_INCLUDE-constant here, so I set the two
	 * Font-Properties as different variables. The PATH_INCLUDE-Constant gets
	 * added in the function that uses this variable
	 */
	protected $_fontRelativePath = '/fonts/verdana.ttf';

	/**
	 * The default size of the font
	 * @var integer
	 */
	protected $_fontSize = 10;

	/**
	 * Default Settings for Antialiasing of the Chart
	 * @var boolean
	 */
	protected $_antialias = false;

}

?>
