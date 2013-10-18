<?php

namespace Tks\TksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deployments
 *
 * @ORM\Table(name="deployments")
 * @ORM\Entity
 */
class Deployment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=25, nullable=false)
     */
    public $name;

    /**
     * @var string
     *
     * @ORM\Column(name="apiToken", type="string", length=250, nullable=true)
     */
    public $apiToken;

    /**
     * @var string
     *
     * @ORM\Column(name="parent", type="integer")
     */
    public $parent;

    /**
     * @var string
     *
     * @ORM\Column(name="created", type="datetime")
     */
    public $created;

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
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
     * @return Deployment
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
     * Set apitoken
     *
     * @param string $apitoken
     * @return Deployment
     */
    public function setApiToken($apitoken)
    {
        $this->apiToken = $apitoken;
    
        return $this;
    }

    /**
     * Get apitoken
     *
     * @return string 
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    public function __toString()
    {
        return strval($this->id);
    }
}