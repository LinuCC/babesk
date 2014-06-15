<?php
require_once PATH_ACCESS . '/TableManager.php';

/**
 * This class contains the names of the globalSettings in the database
 * It acts as an enum
 */
abstract class GlobalSettings {
	const HELPTEXT = 'helptext';
	const WEBLOGIN_HELPTEXT  = 'webLoginHelptext';
	const RELIGION = 'religion';
	const SPECIAL_COURSE = 'special_course';
	const FOREIGN_LANGUAGE = 'foreign_language';
	const FITS_KEY = 'fits_key';
	const FITS_YEAR = 'fits_year';
	const FITS_CLASS = 'fits_class';
	const FITS_ALL_CLASSES = 'fits_all_classes';
	const SOLI_PRICE = 'soli_price';
	const IS_CLASSREGISTRATION_ENABLED = 'isClassRegistrationEnabled';
	const SMTP_HOST = 'smtpHost';
	const SMTP_USERNAME = 'smtpUsername';
	const SMTP_PASSWORD = 'smtpPassword';
	const SMTP_FROMNAME = 'smtpFromName';
	const SMTP_FROM = 'smtpFrom';
	const PRESET_PASSWORD = 'presetPassword';
	const FIRST_LOGIN_CHANGE_PASSWORD = 'firstLoginChangePassword';
	const FIRST_LOGIN_CHANGE_EMAIL = 'firstLoginChangeEmail';
	const FIRST_LOGIN_CHANGE_EMAIL_FORCED = 'firstLoginForceChangeEmail';
	const WEBHP_REDIRECT_DELAY = 'webHomepageRedirectDelay';
	const WEBHP_REDIRECT_TARGET  = 'webHomepageRedirectTarget';
	const ORDER_ENDDATE  = 'orderEnddate';
    const ISSITEUNDERMAINTENANCE  = 'siteIsUnderMaintenance';
}

class GlobalSettingsManager extends TableManager {
	function __construct() {
		parent::__construct('SystemGlobalSettings');
	}

	/**
	 * returns the helptext found in the MySQL-table SystemGlobalSettings
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 * @return string
	 */
	function getHelpText() {
		return $this->valueGet (GlobalSettings::HELPTEXT);
	}

	/**
	 * Changes the HelpText
	 * @param string $str the text will be changed to this string
	 * @throws MySQLVoidDataException
	 * @throws some other things if somethings gone wrong
	 */
	function changeHelpText($str) {
		$this->valueSet (GlobalSettings::HELPTEXT, $str);
	}

	/**
	 * returns the religions found in the MySQL-table SystemGlobalSettings
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 * @return string
	 */
	function getReligion() {
		return $this->valueGet (GlobalSettings::RELIGION);
	}

	/**
	 * Sets the Religions
	 * @param string $str the text will be changed to this string
	 * @throws MySQLVoidDataException
	 * @throws some other things if somethings gone wrong
	 */
	function setReligion($str) {
		$this->valueSet (GlobalSettings::RELIGION, $str);
	}

	//////////////
	///@FIXME Why the hell are the following 4 functions duplicated?!
	///			(setCourse == setSpecialCourses etc)

	/**
	 * returns the courses found in the MySQL-table SystemGlobalSettings
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 * @return string
	 */
	function getCourse() {
		return $this->valueGet (GlobalSettings::SPECIAL_COURSE);
	}

	/**
	 * Sets the Courses
	 * @param string $str the text will be changed to this string
	 * @throws MySQLVoidDataException
	 * @throws some other things if somethings gone wrong
	 */
	function setCourse($str) {
		$this->valueSet (GlobalSettings::SPECIAL_COURSE, $str);
	}

	/**
	 * returns the special courses found in the MySQL-table SystemGlobalSettings
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 * @return string
	 */
	function getSpecialCourses() {
		return $this->valueGet (GlobalSettings::SPECIAL_COURSE);
	}

	/**
	 * Sets the special courses
	 * @param string $str the text will be changed to this string
	 * @throws MySQLVoidDataException
	 * @throws some other things if somethings gone wrong
	 */
	function setSpecialCourses($str) {
		$this->valueSet (GlobalSettings::SPECIAL_COURSE, $str);
	}

	/**
	 * returns the foreign languages found in the MySQL-table SystemGlobalSettings
	 * @throws MySQLVoidDataException
	 * @throws Other Exceptions (@see TableManager)
	 * @return string
	 */
	function getForeignLanguages() {
		return $this->valueGet (GlobalSettings::FOREIGN_LANGUAGE);
	}

	/**
	 * Sets the foreign languages
	 * @param string $str the text will be changed to this string
	 * @throws MySQLVoidDataException
	 * @throws some other things if somethings gone wrong
	 */
	function setForeignLanguages($str) {
		$this->valueSet (GlobalSettings::FOREIGN_LANGUAGE, $str);
	}


	/**
	 * Returns both of the infotexts of the table SystemGlobalSettings
	 * Enter description here ...
	 * @return array [0] = infotext1 [1] = infotext2
	 */
	function getInfoTexts() {
		$it_arr = array();
		$it1 = $this->searchEntry('name="menu_text1"');
		$it2 = $this->searchEntry('name="menu_text2"');

		$it_arr[0] = $it1['value'];
		$it_arr[1] = $it2['value'];

		if (!$it_arr[0] || !$it_arr[1]) {
			throw new MySQLVoidDataException('MySQL returned a void element!');
		}
		return $it_arr;
	}

	/**
	 * Changes infotext1 and infotext2 based on the arguments given
	 */
	function setInfoTexts($infotext1, $infotext2) {
		$this->alterEntry($this->getIDByValue('name', 'menu_text1'), 'value', $infotext1);
		$this->alterEntry($this->getIDByValue('name', 'menu_text2'), 'value', $infotext2);
	}

	/**
	 * returns the value of soli_price
	 * @throws UnexpectedValueException when soli_price is NULL
	 * @throws something else when MySQL has problems
	 * @return string the soli_price
	 */
	function getSoliPrice() {
		return $this->valueGet (GlobalSettings::SOLI_PRICE);
	}

	/**
	 * returns the key for Fits module
	 * @throws UnexpectedValueException when fits is NULL
	 * @throws something else when MySQL has problems
	 * @return string the fits_key
	 */
	function getFitsKey() {
		return $this->valueGet (GlobalSettings::FITS_KEY);
	}

	/**
	 * changes the Fits key
	 * @param unknown_type $key
	 */
	function setFitsKey($key) {
		$this->valueSet (GlobalSettings::FITS_KEY, $key);
	}

	/**
	 * returns the schoolyear for Fits module
	 * @throws UnexpectedValueException when schoolyear is NULL
	 * @throws something else when MySQL has problems
	 * @return string the schoolyear
	 */
	function getFitsYear() {
		return $this->valueGet (GlobalSettings::FITS_YEAR);
	}

	/**
	 * changes the Fits year
	 * @param unknown_type $year
	 */
	function setFitsYear($year) {
		$this->valueSet (GlobalSettings::FITS_YEAR, $year);
	}

	/**
	 * returns the class for Fits module
	 * @throws UnexpectedValueException when class is NULL
	 * @throws something else when MySQL has problems
	 * @return string the class
	 */
	function getFitsClass() {
		return $this->valueGet (GlobalSettings::FITS_CLASS);
	}

	/**
	 * changes the Fits class
	 * @param unknown_type $class
	 */
	function setFitsClass($class) {
		$this->valueSet (GlobalSettings::FITS_CLASS, $class);
	}

	/**
	 * returns the search method for Fits module
	 * @throws UnexpectedValueException when search method is NULL
	 * @throws something else when MySQL has problems
	 * @return boolean the search method
	 */
	function getFitsAllClasses() {
		return $this->valueGet (GlobalSettings::FITS_ALL_CLASSES);
	}

	/**
	 * changes the Fits search methof
	 * @param unknown_type $flag
	 */
	function setFitsAllClasses($flag) {
		$this->valueSet (GlobalSettings::FITS_ALL_CLASSES, $flag);
	}

	/**
	 * Changes the value of "soli_price" to the given value
	 * @throws something if something has gone wrong
	 */
	function changeSoliPrice($value) {
		$this->valueSet (GlobalSettings::SOLI_PRICE, $value);
	}
	
	function getLastOrderTime() {
		return $this->valueGet (GlobalSettings::ORDER_ENDDATE);
	}

	/**
	 * Sets the Global Setting that has the name $name to the value $value
	 * If no entry with this name is found, the entry will be created in the
	 * table with the value $value
	 */
	public function valueSet ($name, $value) {
		try {
			$id = $this->getIDByValue ('name', $name);
		} catch (MySQLVoidDataException $e) {
			//nothing found, create new entry
			$this->addEntry ('name', $name, 'value', $value);
			return;
		}
		$this->alterEntry ($id, 'value', $value);
	}

	/**
	 * Returns the value of the GlobalSettings-entry with the name $name
	 * @throws MySQLVoidDataException if no entry with the name $name was found
	 */
	public function valueGet ($name) {
		try {
			$id = $this->getIDByValue ('name', $name);
			$value = $this->getEntryValue ($id, 'value');
		} catch (MySQLVoidDataException $e) {
			throw new MySQLVoidDataException (sprintf('Global Setting not found: %s', $name));
		}
		return $value;
	}
}
?>
