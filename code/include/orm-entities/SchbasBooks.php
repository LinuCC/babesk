<?php

namespace Babesk\ORM;

/**
 * @Entity
 */
class SchbasBooks {

	/**
	 * @id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $title;

	/**
	 * @Column(type="string")
	 */
	protected $author;

	/**
	 * @Column(type="string")
	 */
	protected $publisher;

	/**
	 * @Column(type="string")
	 */
	protected $isbn;

	/**
	 * @ManyToOne(targetEntity="SchbasSubjects")
	 * @JoinColumn(name="subjectId", referencedColumnName="id")
	 */
	protected $subject;

	/**
	 * @Column(type="string")
	 */
	protected $class;

	/**
	 * @Column(type="integer")
	 */
	protected $bundle;

	/**
	 * @Column(type="float")
	 */
	protected $price;

	public function getId() {
		return $this->id;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function setAuthor($author) {
		$this->author = $author;
		return $this;
	}

	public function getPublisher() {
		return $this->publisher;
	}

	public function setPublisher($publisher) {
		$this->publisher = $publisher;
		return $this;
	}

	public function getIsbn() {
		return $this->isbn;
	}

	public function setIsbn($isbn) {
		$this->isbn = $isbn;
		return $this;
	}

	public function getClass() {
		return $this->class;
	}

	public function setClass($class) {
		$this->class = $class;
		return $this;
	}

	public function getSubject() {
		return $this->subject;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}

	public function getBundle() {
		return $this->bundle;
	}

	public function setBundle($bundle) {
		$this->bundle = $bundle;
		return $this;
	}

	public function getPrice() {
		return $this->price;
	}

	public function setPrice($price) {
		$this->price = $price;
		return $this;
	}
}

?>