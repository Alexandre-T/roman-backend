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
        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setEmail('admin@example.org');
        $admin->setPlainPassword('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $owner = new User();
        $owner->setUsername('Owner');
        $owner->setEmail('owner@example.org');
        $owner->setPlainPassword('owner');
        $owner->setRoles(['ROLE_USER']);
        $manager->persist($owner);

        $user = new User();
        $user->setUsername('User');
        $user->setEmail('user@example.org');
        $user->setPlainPassword('user');
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $book = new Book();
        $book->setTitle('Book of Owner');
        $book->setOwner($owner);
        $manager->persist($book);
        $manager->flush();

        $book = new Book();
        $book->setTitle('Book of Admin');
        $book->setOwner($admin);
        $manager->persist($book);
        $manager->flush();
    }
}
