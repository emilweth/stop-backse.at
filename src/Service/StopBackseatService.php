<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
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

    /**
     * @return array<User>
     */
    public function getLastUnderstoodUsers(int $max, User $except = null): array
    {
        /** @var UserRepository $repository */
        $repository = $this->em->getRepository(User::class);

        return $repository
            ->findLast($max, $except)
        ;
    }
}
