<?php

declare(strict_types=1);

namespace App\Tests;

use Codeception\Util\JsonType;

/**
 * Class ApiCest
 *
 * @package App\Tests
 */
class ApiCest
{
    /**
     * Acceptance test for the API.
     *
     * @param ApiTester $you
     */
    public function tryToAccessAPI(ApiTester $you): void
    {
        $you->wantTo('test the API');
        $you->haveHttpHeader('Content-Type','application/json');
        $you->sendGET('/api/index.jsonld');
        $you->seeResponseCodeIsSuccessful();
        $you->seeResponseContainsJson([
            '@context' => '/api/contexts/Entrypoint',
            '@id' => '/api',
            '@type' => 'Entrypoint',
            'book' => '/api/books',
            'user' => '/api/users',
        ]);
    }
}