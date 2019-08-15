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

namespace App\Factory;

use App\Entity\Book;
use App\Entity\User;

/**
 * Book Factory.
 */
class BookFactory
{
    /**
     * Create a book, set owner and title if title is provided.
     *
     * @param User        $owner Book owner
     * @param string|null $title Book title
     *
     * @return Book
     */
    public static function createBook(User $owner, string $title = null): Book
    {
        $book = new Book();
        $book->setAuthor($owner->getUsername());
        $book->setOwner($owner);

        if (null !== $title) {
            $book->setTitle($title);
        }

        return $book;
    }
}
