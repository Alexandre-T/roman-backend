<?php

namespace App\Tests;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

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
    public function setUp()
    {
        $this->book = new Book();
    }

    /**
     * Test the constructor.
     */
    public function testConstruct()
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
     * Test biography getter and setter.
     */
    public function testGetBiography()
    {
        self::markTestIncomplete();
    }

    /**
     * Test drama pitch getter and setter.
     */
    public function testGetDramaPitch()
    {
        self::markTestIncomplete();
    }

    /**
     * Test owner getter and setter.
     */
    public function testGetOwner()
    {
        self::markTestIncomplete();
    }

    /**
     * Test trajectorial pitch getter and setter.
     */
    public function testGetTrajectorialPitch()
    {
        self::markTestIncomplete();
    }

    /**
     * Test author getter and setter.
     */
    public function testGetAuthor()
    {
        self::markTestIncomplete();
    }

    /**
     * Test title getter and setter.
     */
    public function testGetTitle()
    {
        self::markTestIncomplete();
    }

    /**
     * Test tagline pitch getter and setter.
     */
    public function testGetTaglinePitch()
    {
        self::markTestIncomplete();
    }
}
