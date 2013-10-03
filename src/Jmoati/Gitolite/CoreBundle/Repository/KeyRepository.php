<?php

namespace Jmoati\Gitolite\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Jmoati\Gitolite\CoreBundle\Entity\User;

class KeyRepository extends EntityRepository
{
    public function findByUserQuery(User $user)
    {
        return $this
            ->createQueryBuilder('key')
            ->where('key.user = :user')
            ->setParameter('user', $user)
            ->getQuery();
    }
}
