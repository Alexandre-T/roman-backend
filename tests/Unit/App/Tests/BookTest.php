<?php
/**
 * This file is part of the back-end of Roman application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\Tests;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BookTest extends TestCase
{
    /**
     * Book to unit test.
     *
     * @var Book
     */
    private $book;

    /**
     * Setup the book entity to test.
     */
    public function setUp(): void
    {
        $this->book = new Book();
    }

    /**
     * Test the constructor.
     */
    public function testConstruct(): void
    {
        self::assertNull($this->book->getAuthor());
        self::assertNull($this->book->getBiography());
        self::assertNull($this->book->getDramaPitch());
        self::assertNull($this->book->getId());
        self::assertNull($this->book->getOwner());
        self::assertNull($this->book->getTaglinePitch());
        self::assertNull($this->book->getTitle());
        self::assertNull($this->book->getTrajectorialPitch());
        self::assertNotNull($this->book->getUuid());
        self::assertNotEmpty($this->book->getUuid());
    }

    /**
     * Test author getter and setter.
     */
    public function testGetAuthor(): void
    {
        self::markTestIncomplete();
    }

    /**
     * Test biography getter and setter.
     */
    public function testGetBiography(): void
    {
        self::markTestIncomplete();
    }

    /**
     * Test drama pitch getter and setter.
     */
    public function testGetDramaPitch(): void
    {
        self::markTestIncomplete();
    }

    /**
     * Test owner getter and setter.
     */
    public function testGetOwner(): void
    {
        self::markTestIncomplete();
    }

    /**
     * Test tagline pitch getter and setter.
     */
    public function testGetTaglinePitch(): void
    {
        self::markTestIncomplete();
    }

    /**
     * Test title getter and setter.
     */
    public function testGetTitle(): void
    {
        self::markTestIncomplete();
    }

    /**
     * Test trajectorial pitch getter and setter.
     */
    public function testGetTrajectorialPitch(): void
    {
        self::markTestIncomplete();
    }
}
