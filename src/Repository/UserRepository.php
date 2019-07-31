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

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * UserRepository constructor.
     *
     * @param RegistryInterface $registry registry interface
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Find one user by email.
     *
     * @param string $email the email of user
     *
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.email = :email')
                ->setParameter('email', $email)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            //should not be reached because of unique index on email column
            return null;
        }
    }

    /**
     * Find one user by username.
     *
     * @param string $username the username of user
     *
     * @return User|null
     */
    public function findOneByUsername(string $username): ?User
    {
        try {
            return $this->createQueryBuilder('u')
                ->andWhere('u.username = :username')
                ->setParameter('username', $username)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            //should not be reached because of unique index on username column
            return null;
        }
    }

    /**
     * Load user by its mail.
     *
     * @param string $email user can only be loaded with its email.
     *
     * @return User|UserInterface|null
     */
    public function loadUserByUsername($email)
    {
        return $this->findOneByEmail($email);
    }
}
