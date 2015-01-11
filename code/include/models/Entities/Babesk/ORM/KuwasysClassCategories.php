<?php

namespace Babesk\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * KuwasysClassCategories
 */
class KuwasysClassCategories
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $translatedName;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $classes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->classes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return KuwasysClassCategories
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set translatedName
     *
     * @param string $translatedName
     * @return KuwasysClassCategories
     */
    public function setTranslatedName($translatedName)
    {
        $this->translatedName = $translatedName;

        return $this;
    }

    /**
     * Get translatedName
     *
     * @return string 
     */
    public function getTranslatedName()
    {
        return $this->translatedName;
    }

    /**
     * Add classes
     *
     * @param \Babesk\ORM\KuwasysClasses $classes
     * @return KuwasysClassCategories
     */
    public function addClass(\Babesk\ORM\KuwasysClasses $classes)
    {
        $this->classes[] = $classes;

        return $this;
    }

    /**
     * Remove classes
     *
     * @param \Babesk\ORM\KuwasysClasses $classes
     */
    public function removeClass(\Babesk\ORM\KuwasysClasses $classes)
    {
        $this->classes->removeElement($classes);
    }

    /**
     * Get classes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
