<?php

namespace Babesk\ORM;
use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;

require_once PATH_INCLUDE . '/orm-entities/SchbasBooks.php';
require_once PATH_INCLUDE . '/orm-entities/SchbasLending.php';
require_once PATH_INCLUDE . '/orm-entities/SystemUsers.php';

/**
 * @Entity
 */
class SchbasInventory {

	/**
	 * @Id
	 * @Column(type="integer", name="id")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @ManyToOne(targetEntity="SchbasBooks")
	 * @JoinColumn(name = "book_id", referencedColumnName = "id")
	 */
	protected $book;

	/**
	 * @OneToMany(targetEntity="SchbasLending", mappedBy="inventory")
	 */
	protected $lending;

	/**
	 * @Column(type="integer", name="year_of_purchase")
	 */
	protected $yearOfPurchase;

	/**
	 * @Column(type="integer")
	 */
	protected $exemplar;

	/**
	 * @ManyToMany(targetEntity="SystemUsers", mappedBy="booksLent")
	 */
	protected $usersLent;

	public function getId() {
		return $this->id;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function getBook() {
		return $this->book;
	}

	public function setBook($book) {
		$this->book = $book;
		return $this;
	}

	public function getYearOfPurchase() {
		return $this->yearOfPurchase;
	}

	public function setYearOfPurchase($yearOfPurchase) {
		$this->yearOfPurchase = $yearOfPurchase;
		return $this;
	}

	public function getExemplar() {
		return $this->exemplar;
	}

	public function setExemplar($exemplar) {
		$this->exemplar = $exemplar;
		return $this;
	}

	public function getUsersLent() {
		return $this->usersLent;
	}

	public function setUsersLent($usersLent) {
		$this->usersLent = $usersLent;
		return $this;
	}

	public function __construct() {

		$this->usersLent = new ArrayCollection();
	}
}

?>