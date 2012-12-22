<?php

require_once PATH_INCLUDE . '/constants.php';
require_once 'UsernameScheme.php';

/**
 * Allows for automatic creation of Usernames based on predefined rules
 */
class UsernameAutoCreator {
	////////////////////////////////////////////////////////////////////////////////
	//Constructor
	////////////////////////////////////////////////////////////////////////////////
	public function __construct () {
	}
	////////////////////////////////////////////////////////////////////////////////
	//Getters and Setters
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * Sets the users so that they can get usernames later on
	 * @param $users An Array of an Array, containing the database-Values of the
	 *	Users
	 */
	public function usersSet ($users) {
		$this->users = $users;
	}

	/**
	 * Sets the scheme of the usernames,
	 * @see UsernameScheme
	 * @param $scheme A instance of UsernameScheme
	 */
	public function schemeSet ($scheme) {
		$this->scheme = $scheme;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Methods
	////////////////////////////////////////////////////////////////////////////////
	/**
	 * Creates Usernames for all the Users which were set beforehands with
	 * UsernameAutoCreator::usersSet. Also it needs a scheme to create the username,
	 * so make sure you called UsernameAutoCreator::schemeSet ()
	 */
	public function usernameCreateAll () {
		$result = array ();
		foreach ($this->users as $user) {
			$user ['username'] = $this->scheme->schemeUseOnUser ($user);
			$result [] = $user;
		}
		return $result;
	}

	////////////////////////////////////////////////////////////////////////////////
	//Implementations
	////////////////////////////////////////////////////////////////////////////////

	////////////////////////////////////////////////////////////////////////////////
	//Attributes
	////////////////////////////////////////////////////////////////////////////////

	protected $scheme;
	protected $users;

}

?>