<?php

declare(strict_types=1);

namespace App\Tests;

/**
 * Class ApiCest
 */
class RomanCest
{
    public function tryToAccessAPI(AcceptanceTester $you): void
    {
        $you->wantTo('test the API');

        $you->amOnPage('/api');
        $you->seeResponseCodeIsSuccessful();
    }
}