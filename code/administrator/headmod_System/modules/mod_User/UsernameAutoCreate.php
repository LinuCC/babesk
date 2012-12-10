<?php

require_once PATH_INCLUDE . '/constants.php';

class UsernameScheme {

	public function __construct () {
		$this->scheme = array ();
	}

	public function templateAdd ($templateName) {
		$this->scheme [] = '|T:|' . $templateName;
	}

	public function stringAdd ($stringName) {
		$this->scheme [] = '|S:|' . $stringName;
	}

	protected function schemeUseOnUser ($user) {
		$templateStr = explode('|T:|', $this->scheme);
		$contentStr = array();
		$result = '';
		while (strstr($templateStr [0], '|S:|')) {
			$contentStr [] = array_slice($templateStr, 1);
		}
		$result = $this->stringAddArray ($contentStr, $result);
		foreach ($templateStr as $tmp) {
			$str = explode('|S:|', $tmp);
			$result .= $user [$str [0]];
			array_slice($tmp, 1);
			$result = $this->stringAddArray ($tmp, $result);
		}
		var_dump($result);
	}

	protected function stringAddArray ($strArray, $result) {
		foreach ($strArray as $con) {
				$result .= $con;
		}
		return $result;
	}

	protected string $scheme;

	const $Forename = 'forename';
	const $Name = 'name';
	const $Email = 'email';
	const $Birthday = 'birthday';
}

class UsernameAutoCreate {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct () {

	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	public function usersSet () {

	}
	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * Not good function, replace it with something smoother
	 */
	public function createBy ($forename, $name)

	public function usernameCreateAllBy () {

	}
	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////
	protected function usernameCreateByScheme () {

	}
	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	protected $users;

}

?>