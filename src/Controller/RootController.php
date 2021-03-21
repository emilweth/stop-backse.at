<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RootController extends AbstractController
{
    public function root(Request $request): RedirectResponse
    {
        return $this->redirectToRoute('home', [
            '_locale' => ('fr' === $request->getLocale() ? 'fr' : 'en'),
        ]);
    }
}
