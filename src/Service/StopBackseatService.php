<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class StopBackseatService
{
    public function __construct(
        public EntityManagerInterface $em
    ) {
    }

    public function userUnderstood(User &$user): void
    {
        if (!$user->getUnderstood()) {
            $user->setUnderstood(true);
            $this->em->persist($user);
            $this->em->flush();
        }
    }

    public function getLastUnderstoodUsers(int $max, User $except = null)
    {
        return $this->em->getRepository(User::class)
            ->findLast($max, $except)
        ;
    }
}
