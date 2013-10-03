<?php

namespace Jmoati\Gitolite\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Jmoati\HelperBundle\Traits\Entity;
use Jmoati\HelperBundle\Traits\Sluggable;
use Jmoati\HelperBundle\Traits\Timestampable;

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
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Key
     */
    public function setValue($value)
    {
        $this->value = $value;

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
     * @param User $user
     *
     * @return Key
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Repository $repository
     *
     * @return Key
     */
    public function setRepository(Repository $repository = null)
    {
        $this->repository = $repository;

        return $this;
    }
}
