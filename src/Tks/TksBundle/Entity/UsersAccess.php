<?php

namespace Tks\TksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersAccess
 *
 * @ORM\Table(name="users_access")
 * @ORM\Entity
 */
class UsersAccess
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     * @ORM\Column(name="can_write", type="boolean")
     */
    public $canWrite;

    /**
     * @var \Tks\TksBundle\Entity\Deployment
     * @ORM\Column(name="deployment_id", type="integer")
     */
    public $deployment;

    /**
     * @var \User
     * @ORM\Column(name="user_id")
     */
    private $user;


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
     * Set canWrite
     *
     * @param boolean $canWrite
     * @return UsersAccess
     */
    public function setCanWrite($canWrite)
    {
        $this->canWrite = $canWrite;
    
        return $this;
    }

    /**
     * Get canWrite
     *
     * @return boolean 
     */
    public function getCanWrite()
    {
        return $this->canWrite;
    }

    /**
     * Set deployment
     *
     * @param \Tks\TksBundle\Entity\Deployment $deployment
     * @return UsersAccess
     */
    public function setDeployment(\Tks\TksBundle\Entity\Deployment $deployment = null)
    {
        $this->deployment = $deployment;
    
        return $this;
    }

    /**
     * Get deployment
     *
     * @return \Tks\TksBundle\Entity\Deployments 
     */
    public function getDeployment()
    {
        return $this->deployment;
    }

    /**
     * Set user
     *
     * @param \Tks\TksBundle\Entity\User $user
     * @return UsersAccess
     */
    public function setUser(\Tks\TksBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Tks\TksBundle\Entity\Users 
     */
    public function getUser()
    {
        return $this->user;
    }
}
