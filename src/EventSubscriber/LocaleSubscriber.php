<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->headers->has('Accept-Language')) {
            switch ($request->headers->get('Accept-Language')) {
                case 'fr':
                    $request->setLocale('fr');

                    break;

                case 'en':
                default:
                    $request->setLocale('en');
            }
        }
    }

    #[ArrayShape(['kernel.request' => 'string'])]
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
