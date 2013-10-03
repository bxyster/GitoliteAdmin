<?php

namespace Jmoati\Gitolite\CoreBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Jmoati\Gitolite\CoreBundle\Entity\User;

class RepositoryRepository extends EntityRepository
{
    public function findByUserQuery(User $user, $owner = true, $developer = true, $viewer = true)
    {
        if (!$owner && !$developer && !$viewer) {
            return new ArrayCollection();
        }

        $select = array('repository');

        $qb = $this
            ->createQueryBuilder('repository')
            ->leftJoin('repository.owner', 'owner')
            ->leftJoin('repository.developers', 'developer')
            ->leftJoin('repository.viewers', 'viewer');

        if ($owner) {
            $qb = $qb
                ->orWhere('owner = :user');

            $select[] = 'owner';
        }

        if ($developer) {
            $qb = $qb
                ->orWhere('developer = :user');

            $select[] = 'developer';
        }

        if ($viewer) {
            $qb = $qb
                ->orWhere('viewer = :user');

            $select[] = 'viewer';
        }

        $qb = $qb
            ->select($select)
            ->setParameter('user', $user)
            ->getQuery();

        return $qb;
    }
}
