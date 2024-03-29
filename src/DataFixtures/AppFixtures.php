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
use App\Entity\ObfuscatedInterface;
use App\Entity\User;
use App\Factory\BookFactory;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * App Fixtures load all tests fixtures.
 */
class AppFixtures extends Fixture
{
    /**
     * Load data.
     *
     * @param ObjectManager $manager the manager to store data in database
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        if (in_array($_ENV['APP_ENV'], ['dev', 'test'], true)) {
            $admin = $this->createUser('admin', true, 1);
            $owner = $this->createUser('owner', false, 2);
            $user = $this->createUser('user', false, 3);
            $inactive = $this->createUser('inactive', false, 4, false);

            $bookOfAdmin = $this->createBook('Book of admin', $admin, 1);
            $firstBookOfOwner = $this->createBook('First book of owner', $owner, 2);
            $secondBookOfOwner = $this->createBook('Second book of owner', $owner, 3);

            $manager->persist($admin);
            $manager->persist($owner);
            $manager->persist($user);
            $manager->persist($inactive);
            $manager->persist($bookOfAdmin);
            $manager->persist($firstBookOfOwner);
            $manager->persist($secondBookOfOwner);

            $manager->flush();
        }
    }

    /**
     * Create a book for a user.
     *
     * @param string $title      Title of the book
     * @param User   $owner      Owner of the book
     * @param int    $identifier Identifier used by tests
     *
     * @return Book
     */
    private function createBook(string $title, User $owner, int $identifier): Book
    {
        $book = BookFactory::createBook($owner, $title);

        //Change the UUID for tests
        $this->updateUuid($book, $identifier);

        return $book;
    }

    /**
     * Create a user.
     *
     * @param string $nickname   the nickname used to generation nickname, password and email
     * @param bool   $isAdmin    is this user an admin?
     * @param int    $identifier the identifier
     * @param bool   $isActive   is this user active?
     *
     * @throws Exception
     *
     * @return User
     */
    private function createUser(string $nickname, bool $isAdmin, int $identifier, bool $isActive = true): User
    {
        $user = new User();
        $user->setNickname(ucfirst($nickname));
        $user->setEmail($nickname.'@example.org');
        $user->setPlainPassword($nickname);
        $user->setRoles(['ROLE_USER']);
        $user->activate();
        $user->requestReceived();
        $user->setRenewCode($nickname.'-renew-code');

        if ($isAdmin) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        if (!$isActive) {
            $user->inactivate();
            $user->setRenewAt(new DateTimeImmutable('now - 11 hours'));
        }

        //Change the UUID for tests
        $this->updateUuid($user, $identifier);
        $this->updateActivationCode($user);

        return $user;
    }

    /**
     * Reflect a Activation class to access its private activation code property.
     *
     * @param User $entity the class to update
     *
     * @return User
     */
    private function updateActivationCode(User $entity): User
    {
        try {
            $reflection = new ReflectionClass($entity);
            $property = $reflection->getProperty('activationCode');
            $property->setAccessible(true);
            $property->setValue($entity, strtolower($entity->getNickname()).'-activation-code');
        } catch (ReflectionException $e) {
            //This cannot be reached because User use ActivationTrait.
            return $entity;
        }

        return $entity;
    }

    /**
     * Reflect a Obfuscated class to access its private uuid property.
     *
     * @param ObfuscatedInterface $obfuscated the class to update
     * @param int                 $identifier the new identifier
     *
     * @return ObfuscatedInterface
     */
    private function updateUuid(ObfuscatedInterface $obfuscated, int $identifier): ObfuscatedInterface
    {
        $format = 'aaaaaaaa-1234-%04d-bbbbbbbbbbbbbbbbb';

        if ($obfuscated instanceof Book) {
            $format = 'aaaaaaaa-b00c-%04d-bbbbbbbbbbbbbbbbb';
        } elseif ($obfuscated instanceof User) {
            $format = 'aaaaaaaa-0000-%04d-bbbbbbbbbbbbbbbbb';
        }

        $uuid = sprintf($format, $identifier);
        try {
            $reflection = new ReflectionClass($obfuscated);
            $property = $reflection->getProperty('uuid');
            $property->setAccessible(true);
            $property->setValue($obfuscated, $uuid);
        } catch (ReflectionException $e) {
            //This cannot be reached because Book use ObfuscatedTrait.
            return $obfuscated;
        }

        return $obfuscated;
    }
}
