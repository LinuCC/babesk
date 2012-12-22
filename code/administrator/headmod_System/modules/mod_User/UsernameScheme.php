<?php
/**
 * Used to allow to create Schemes for Creating Usernames.
 * Instances of this class then can be used in UsernameAutoCreator.
 * use UsernameScheme::templateAdd and UsernameScheme::stringAdd to design the
 * Scheme, and UsernameScheme::schemeUseOnUser to parse the scheme and
 * hopefully get something useful.
 */
class UsernameScheme {

	public function __construct () {
		$this->scheme = array ();
	}

	/**
	 * Adds a template to the Scheme. These Templates are defined as the constants
	 * that this class has. It will try to replace these Templates with actual
	 * values when calling parsing functions
	 */
	public function templateAdd ($templateName) {
		$this->scheme [] = self::$tmpDelimiter . $templateName;
	}

	/**
	 * Adds a string to the scheme. These "content"-strings are not parsed,
	 * instead they will just be added to the result-string when parsing.
	 */
	public function stringAdd ($stringName) {
		$this->scheme [] = self::$contentDelimiter . $stringName;
	}

	/**
	 * Parses the scheme using a User. It tries to replace the Templates
	 * with the values of the  keys with the same name as the templates.
	 * @return string The ResultString
	 */
	public function schemeUseOnUser ($user) {
		$result = '';
		foreach ($this->scheme as $str) {
			$result .= self::schemeUseOnUserRoutine ($str, $user);
		}
		return $result;
	}

	/**
	 * the main-routine of schemeUseOnUser
	 * @used-by UsernameScheme::schemeUseOnUser
	 */
	protected static function schemeUseOnUserRoutine ($str, $user) {
		if (strstr ($str, self::$tmpDelimiter)) {
			$result = self::tmpParseByUser ($str, $user);
		}
		else if (strstr ($str, self::$contentDelimiter)) {
			$result = self::contentParse ($str);
		}
		else {
			throw new Exception ('Internal Problem: Could not find Delimiter-needle');
		}
		return $result;
	}

	/**
	 *Parses a template by replacing the template with the actual value of the user
	 */
	protected static function tmpParseByUser ($tmp, $user) {
		$tmp = self::delimiterRemove ($tmp);
		$result = $user [$tmp];
		return $result;
	}

	/**
	 * Parses a content-string.
	 */
	protected static function contentParse ($content) {
		return self::delimiterRemove ($content);
	}

	/**
	 * Removes the delimiter of a string. It does not check if the Delimiter is
	 * really existing at the beginning of the string! It will delete the first
	 * $delimiterLength Characters of it!
	 */
	protected static function delimiterRemove ($str) {
		return substr ($str, self::$delimiterLength);
	}

	public $scheme;

	protected static $tmpDelimiter = '|T:|';
	protected static $contentDelimiter = '|C:|';
	protected static $delimiterLength = 4;

	const Forename = 'forename';
	const Name = 'name';
	const Email = 'email';
	const Birthday = 'birthday';
}
?>