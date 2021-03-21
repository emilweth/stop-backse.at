<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\StopBackseatService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IUnderstandController extends AbstractController
{
    public function __construct(public StopBackseatService $svc)
    {
    }

    /**
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->svc->userUnderstood($user);

        return $this->render('i_understand.html.twig', [
            'user_name' => $this->getUser()->getUsername(),
            'last_users' => $this->svc->getLastUnderstoodUsers(10, $user),
        ]);
    }
}
