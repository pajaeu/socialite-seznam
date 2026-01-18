<?php

declare(strict_types=1);

namespace Pajaeu\SocialiteSeznam;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

final class SocialiteSeznamProvider extends AbstractProvider
{
    private const string AUTH_URL = 'https://login.szn.cz/api/v1/oauth/auth';

    private const string TOKEN_URL = 'https://login.szn.cz/api/v1/oauth/token';

    private const string USER_URL = 'https://api.github.com/user';

    /** @var string[] */
    protected $scopes = [
        'identity',
    ];

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase(self::AUTH_URL, $state);
    }

    protected function getTokenUrl(): string
    {
        return self::TOKEN_URL;
    }

    /** {@inheritdoc} */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            self::USER_URL, $this->getRequestOptions($token)
        );

        return json_decode((string) $response->getBody(), true);
    }

    /** {@inheritdoc} */
    protected function mapUserToObject(array $user)
    {
        $firstName = Arr::get($user, 'firstname');
        $lastName = Arr::get($user, 'lastname');
        $name = sprintf('%s %s', $firstName, $lastName);

        return (new User)->setRaw($user)->map([
            'id' => Arr::get($user, 'oauth_user_id'),
            'nickname' => Arr::get($user, 'username'),
            'name' => $name,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => Arr::get($user, 'email'),
        ]);
    }

    protected function getRequestOptions(string $token): array
    {
        return [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Authorization' => sprintf('Bearer %s', $token),
            ],
        ];
    }
}
