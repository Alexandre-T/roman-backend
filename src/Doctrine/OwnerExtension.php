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

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Book;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

/**
 * Owner Extension class.
 */
final class OwnerExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * OwnerExtension constructor.
     *
     * @param Security $security the security layer
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Modification apply to each collection.
     *
     * @param QueryBuilder                $queryBuilder       The query builder
     * @param QueryNameGeneratorInterface $queryNameGenerator The query name generator
     * @param string                      $resourceClass      The resource class
     * @param string|null                 $operationName      The operation name
     */
    public function applyToCollection(
     QueryBuilder $queryBuilder,
     QueryNameGeneratorInterface $queryNameGenerator,
     string $resourceClass,
     string $operationName = null
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * Modification apply to each item.
     *
     * @param QueryBuilder                $queryBuilder       The query builder
     * @param QueryNameGeneratorInterface $queryNameGenerator The query name generator (unused)
     * @param string                      $resourceClass      The resource class (as a string not as an object)
     * @param array                       $identifiers        The identifiers
     * @param string|null                 $operationName      The operation name
     * @param array                       $context            The context
     */
    public function applyToItem(
     QueryBuilder $queryBuilder,
     QueryNameGeneratorInterface $queryNameGenerator,
     string $resourceClass,
     array $identifiers,
     string $operationName = null,
     array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    /**
     * Add a where clause to limit data to owner if user is not an admin.
     *
     * @param QueryBuilder $queryBuilder  the query builder
     * @param string       $resourceClass the class name, nut not the object
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $user = $this->security->getUser();

        if (null === $user || $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        if (Book::class === $resourceClass) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.owner = :current_user', $rootAlias));
            $queryBuilder->setParameter('current_user', $user);
        }
    }
}
