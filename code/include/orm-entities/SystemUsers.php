<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;

require_once PATH_INCLUDE . '/orm-entities/SystemGroups.php';
require_once PATH_INCLUDE . '/orm-entities/SchbasInventory.php';

/**
 * @Entity @Table(name="SystemUsers")
 */
class SystemUsers {

	/**
	 * @Id
	 * @Column(type="integer",name="ID")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $name;

	/**
	 * @Column(type="string")
	 */
	protected $forename;

	/**
	 * @Column(type="string")
	 */
	protected $username;

	/**
	 * @Column(type="string")
	 */
	protected $password;

	/**
	 * @Column(type="string")
	 */
	protected $email;

	/**
	 * @Column(type="string")
	 */
	protected $telephone;

	/**
	 * @Column(type="string")
	 */
	protected $birthday;

	/**
	 * @Column(type="string")
	 */
	protected $last_login;

	/**
	 * @Column(type="integer")
	 */
	protected $login_tries;

	/**
	 * @Column(type="boolean")
	 */
	protected $first_passwd;

	/**
	 * @Column(type="boolean")
	 */
	protected $locked;

	/**
	 * @Column(type="integer")
	 */
	protected $gid;

	/**
	 * @Column(type="float")
	 */
	protected $credit;

	/**
	 * @Column(type="boolean")
	 */
	protected $soli;

	/**
	 * @Column(type="string")
	 */
	protected $religion;

	/**
	 * @Column(type="string")
	 */
	protected $foreign_language;

	/**
	 * @Column(type="string")
	 */
	protected $course;

	/**
	 * @Column(type="string")
	 */
	protected $special_course;

	/**
	 * @ManyToMany(targetEntity="SystemGroups")
	 * @JoinTable(name="SystemUsersInGroups",
	 *     joinColumns= {@JoinColumn(
	 *         name="userId", referencedColumnName="ID"
	 *     )},
	 *     inverseJoinColumns={@JoinColumn(
	 *         name="groupId", referencedColumnName="ID"
	 *     )}
	 * )
	 */
	protected $groups;

	/**
	 * @ManyToMany(targetEntity="SchbasInventory")
	 * @JoinTable(
	 *     name="SchbasLending",
	 *     joinColumns = {
	 *         @JoinColumn(
	 *             name = "user_id", referencedColumnName = "ID"
	 *         )
	 *     },
	 *     inverseJoinColumns = {
	 *         @JoinColumn(
	 *             name = "inventory_id", referencedColumnName = "id"
	 *         )
	 *     }
	 * )
	 */
	protected $lentBooks;


	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	public function getForename() {
		return $this->forename;
	}

	public function setForename($forename) {
		$this->forename = $forename;
		return $this;
	}

	public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	public function getTelephone() {
		return $this->telephone;
	}

	public function setTelephone($telephone) {
		$this->telephone = $telephone;
		return $this;
	}

	public function getBirthday() {
		return $this->birthday;
	}

	public function setBirthday($birthday) {
		$this->birthday = $birthday;
		return $this;
	}

	public function getLastLogin() {
		return $this->last_login;
	}

	public function setLastLogin($last_login) {
		$this->last_login = $last_login;
		return $this;
	}

	public function getLoginTries() {
		return $this->login_tries;
	}

	public function setLoginTries($login_tries) {
		$this->login_tries = $login_tries;
		return $this;
	}

	public function getFirstPasswd() {
		return $this->first_passwd;
	}

	public function setFirstPasswd($first_passwd) {
		$this->first_passwd = $first_passwd;
		return $this;
	}

	public function getLocked() {
		return $this->locked;
	}

	public function setLocked($locked) {
		$this->locked = $locked;
		return $this;
	}

	public function getGid() {
		return $this->gid;
	}

	public function setGid($gid) {
		$this->gid = $gid;
		return $this;
	}

	public function getCredit() {
		return $this->credit;
	}

	public function setCredit($credit) {
		$this->credit = $credit;
		return $this;
	}

	public function getSoli() {
		return $this->soli;
	}

	public function setSoli($soli) {
		$this->soli = $soli;
		return $this;
	}

	public function getReligion() {
		return $this->religion;
	}

	public function setReligion($religion) {
		$this->religion = $religion;
		return $this;
	}

	public function getForeignLanguage() {
		return $this->foreign_language;
	}

	public function setForeignLanguage($foreign_language) {
		$this->foreign_language = $foreign_language;
		return $this;
	}

	public function getCourse() {
		return $this->course;
	}

	public function setCourse($course) {
		$this->course = $course;
		return $this;
	}

	public function getSpecialCourse() {
		return $this->special_course;
	}

	public function setSpecialCourse($special_course) {
		$this->special_course = $special_course;
		return $this;
	}

	public function getLentBooks() {
		return $this->lentBooks;
	}

	public function setLentBooks($lentBooks) {
		$this->lentBooks = $lentBooks;
		return $this;
	}

	public function __construct() {

		// $this->birthday = new DateTime();
		$this->groups = new ArrayCollection();
		$this->lentBooks = new ArrayCollection();
	}
}

?>