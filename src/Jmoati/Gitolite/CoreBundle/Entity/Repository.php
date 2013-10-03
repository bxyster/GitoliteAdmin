<?php

namespace Jmoati\Gitolite\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Jmoati\HelperBundle\Traits\Entity;
use Jmoati\HelperBundle\Traits\Sluggable;
use Jmoati\HelperBundle\Traits\Timestampable;

/**
 * @ORM\Entity(repositoryClass="Jmoati\Gitolite\CoreBundle\Repository\RepositoryRepository")
 * @ORM\Table(name="gitolite_repository")
 */
class Repository
{
    use Entity;
    use Sluggable;
    use Timestampable;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $public;
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $website;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Key", mappedBy="repository", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $keys;
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $owner;
    /**
     * @var ArrayCollection
     *
     * @ORM\JoinTable(name="gitolite_repository_developer")
     * @ORM\ManyToMany(targetEntity="User")
     */
    protected $developers;
    /**
     * @var ArrayCollection
     *
     * @ORM\JoinTable(name="gitolite_repository_viewer")
     * @ORM\ManyToMany(targetEntity="User")
     */
    protected $viewers;

    public function __construct()
    {
        $this->public     = false;
        $this->developers = new ArrayCollection();
        $this->viewers    = new ArrayCollection();
        $this->keys       = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Repository
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getPublicStr()
    {
        return $this->isPublic() ? 'public' : 'private';
    }

    /**
     * @return boolean
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @param boolean $public
     *
     * @return Repository
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     *
     * @return Repository
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @param Key $key
     *
     * @return Repository
     */
    public function addKey(Key $key)
    {
        $key->setRepository($this);
        $this->keys->add($key);

        return $this;
    }

    /**
     * @param Key $key
     */
    public function removeKey(Key $key)
    {
        $key->setRepository(null);
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
     * @param User $user
     *
     * @return Repository
     */
    public function addUser(User $user)
    {
        $this->developers->add($user);

        return $this;
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->developers->removeElement($user);
    }

    public function getRole(User $user)
    {
        if ($this->isOwner($user)) {
            return 'owner';
        } elseif ($this->isDeveloper($user)) {
            return 'developer';
        } elseif ($this->isViewer($user)) {
            return 'viewer';
        } else {
            return 'public';
        }
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isOwner(User $user)
    {
        return $this->getOwner() == $user;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     *
     * @return Repository
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    public function isDeveloper(User $user)
    {
        return $this->getDevelopers()->contains($user);
    }

    /**
     * @return ArrayCollection
     */
    public function getDevelopers()
    {
        return $this->developers;
    }

    public function isViewer(User $user)
    {
        return $this->getViewers()->contains($user);
    }

    /**
     * @return ArrayCollection
     */
    public function getViewers()
    {
        return $this->viewers;
    }
}
