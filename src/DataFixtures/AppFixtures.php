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

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\User;
use App\Factory\BookFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager the manager to store data in database
     */
    public function load(ObjectManager $manager): void
    {
        $admin = $this->createUser('admin', true);
        $owner = $this->createUser('owner', false);
        $user = $this->createUser('user', false);

        $bookOfAdmin = $this->createBook('Book of admin', $admin);
        $firstBookOfOwner = $this->createBook('First book of owner', $owner);
        $secondBookOfOwner = $this->createBook('Second book of owner', $owner);

        $manager->persist($admin);
        $manager->persist($owner);
        $manager->persist($user);
        $manager->persist($bookOfAdmin);
        $manager->persist($firstBookOfOwner);
        $manager->persist($secondBookOfOwner);

        $manager->flush();
    }

    /**
     * Create a book for a user.
     *
     * @param string $title Title of the book
     * @param User   $owner Owner of the book
     *
     * @return Book
     */
    private function createBook(string $title, User $owner): Book
    {
        return BookFactory::createBook($owner, $title);
    }

    /**
     * Create a user.
     *
     * @param string $nickname the nickname used to generation nickname, password and email
     * @param bool   $isAdmin  is this user an admin?
     *
     * @return User
     */
    private function createUser(string $nickname, bool $isAdmin): User
    {
        $user = new User();
        $user->setNickname(ucfirst($nickname));
        $user->setEmail($nickname.'@example.org');
        $user->setPlainPassword($nickname);
        $user->setRoles(['ROLE_USER']);

        if ($isAdmin) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        return $user;
    }
}
