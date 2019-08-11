<?php

declare(strict_types=1);

namespace App\Tests;

use Codeception\Util\JsonType;
use Flow\JSONPath\JSONPath;

/**
 * Class ApiCest
 */
class BookCrudCest
{
    /**
     * Acceptance test for the API.
     *
     * @param ApiTester $you the api tester injected by dependency
     */
    public function tryToCrudBooks(ApiTester $you): void
    {
        $you->wantTo('CRUD The books');
        $you->sendPOST(
            '/authentication_token', [
                'email' => 'admin@example.org',
                'password' => 'admin',
            ]
        );
        $you->seeResponseCodeIsSuccessful();

        $token = $you->grabDataFromResponseByJsonPath('$.token');

        $you->amBearerAuthenticated($token);
        $you->sendGET('/api/books.jsonld');
        $you->seeResponseCodeIsSuccessful();
        $you->seeResponseContainsJson(
            [
                '@context' => '/api/contexts/Book',
                '@id' => '/api/books',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    '@type' => 'Book',
                ],
            ]
        );
        $you->seeHydraMemberMatchesJsonType(
            [
            'biography' => 'string|null',
            'dramaPitch' => 'string|null',
            'taglinePitch' => 'string|null',
            'title' => 'string',
            'trajectorialPitch' => 'string|null',
            'uuid' => 'string',
            ]
        );
    }
}
