<?php

namespace Jmoati\Gitolite\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jmoati\HelperBundle\Traits\Entity;
use Jmoati\HelperBundle\Traits\Sluggable;
use Jmoati\HelperBundle\Traits\Timestampable;
use Jmoati\Gitolite\CoreBundle\Entity\User;
use Jmoati\Gitolite\CoreBundle\Entity\Repository;

/**
 * @ORM\Entity(repositoryClass="Jmoati\Gitolite\CoreBundle\Repository\KeyRepository")
 * @ORM\Table(name="gitolite_key")
 */
class Key
{
    use Entity;
    use Sluggable;
    use Timestampable;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="keys")
     */
    protected $user;
    /**
     * @var Repository
     *
     * @ORM\ManyToOne(targetEntity="Repository", inversedBy="keys")
     */
    protected $repository;


    /**
     * @param string  $value
     * @return Key
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }
    

    /**
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param User  $user
     * @return Key
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }
    

    /**
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Repository  $repository
     * @return Key
     */
    public function setRepository(Repository $repository = null)
    {
        $this->repository = $repository;
    
        return $this;
    }
    

    /**
     * @return Repository 
     */
    public function getRepository()
    {
        return $this->repository;
    }
}