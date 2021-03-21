<?php

declare(strict_types=1);

namespace App\Service;

use Depotwarehouse\OAuth2\Client\Twitch\Provider\Twitch;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class TwitchAuthenticationService.
 */
class TwitchAuthenticationService
{
    public const AUTH_URL = 'https://id.twitch.tv/oauth2/authorize';
    public const TOKEN_URL = 'https://id.twitch.tv/oauth2/token';

    private HttpClientInterface $httpClient;

    private string $clientId;

    private string $clientSecret;

    private string $redirectUrl;

    private RouterInterface $router;

    private SessionInterface $sessionInterface;

    private Twitch $provider;

    /**
     * TwitchAuthenticationService constructor.
     */
    public function __construct(HttpClientInterface $httpClient, RouterInterface $router, SessionInterface $sessionInterface, Twitch $provider)
    {
        $this->httpClient = $httpClient;
        $this->router = $router;
        $this->sessionInterface = $sessionInterface;
        $this->provider = $provider;
    }

    public function setRedirectUrl(string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function setCredentials(string $clientId, string $clientSecret): void
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @throws Exception
     */
    public function authorizeUrl(): string
    {
        $this->sessionInterface->set('loginToken', md5(random_int(\PHP_INT_MIN, \PHP_INT_MAX)));

        $params = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'response_type' => 'code',
            'force_verify' => 'true',
            'state' => $this->sessionInterface->get('loginToken'),
        ]);

        return self::AUTH_URL . '?' . $params;
    }
}
