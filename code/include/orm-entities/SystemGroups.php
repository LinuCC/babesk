<?php

namespace Babesk\ORM;

/**
 * @Entity @Table(name="SystemGroups")
 */
class SystemGroups {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $name;
}

?>