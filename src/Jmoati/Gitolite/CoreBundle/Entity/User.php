<?php

namespace Jmoati\Gitolite\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as FosUser;
use Jmoati\HelperBundle\Traits\Timestampable;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gitolite_user")
 */
class User extends FosUser
{

    use Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var string $slug
     *
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"username"})
     */
    protected $slug;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Key", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $keys;

    public function __construct()
    {
        parent::__construct();

        $this->keys = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Key $key
     *
     * @return User
     */
    public function addKey(Key $key)
    {
        $key->setUser($this);
        $this->keys->add($key);

        return $this;
    }

    /**
     * @param Key $key
     */
    public function removeKey(Key $key)
    {
        $key->setUser(null);
        $this->keys->removeElement($key);
    }

    /**
     * @return ArrayCollection
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

}
