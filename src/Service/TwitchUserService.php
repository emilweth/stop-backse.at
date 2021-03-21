<?php

declare(strict_types=1);

namespace App\Service;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TwitchApi\Exceptions\TwitchApiException;

/**
 * Class TwitchUserService.
 */
class TwitchUserService
{
    private HttpClientInterface $httpClient;

    private string $twitchClientId;

    /**
     * TwitchUserService constructor.
     */
    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function setTwitchClientId(string $twitchClientId): void
    {
        $this->twitchClientId = $twitchClientId;
    }

    /**
     * @throws \JsonException
     * @throws TransportExceptionInterface
     * @throws TwitchApiException
     *
     * @return array<string, string>
     */
    #[ArrayShape(['twitchId' => 'mixed', 'twitchLogin' => 'mixed', 'twitchDisplayname' => 'mixed', 'twitchProfileUrl' => 'mixed'])]
    public function fetchUserByBearerToken(string $token): array
    {
        $url = 'https://api.twitch.tv/helix/users';
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/vnd.twitchtv.v5+json',
            'Client-ID' => $this->twitchClientId,
        ];

        $request = $this->httpClient->request('GET', $url, [
            'headers' => $headers,
        ]);

        try {
            $result = $request->getContent();
        } catch (ClientExceptionInterface | TransportExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e) {
            throw new TwitchApiException('Error while trying to get bearer token', $e->getCode());
        }

        if (200 !== $request->getStatusCode()) {
            throw new BadCredentialsException('authentication error');
        }

        $twitchUser = json_decode($result, true, 512, \JSON_THROW_ON_ERROR)['data'][0];

        return [
            'twitchId' => $twitchUser['id'],
            'twitchLogin' => $twitchUser['login'],
            'twitchDisplayname' => $twitchUser['display_name'],
            'twitchProfileUrl' => $twitchUser['profile_image_url'],
        ];
    }
}
