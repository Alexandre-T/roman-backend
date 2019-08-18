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

namespace App\Tests\Behat\Context;

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behatch\Context\RestContext;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface as EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface as JWTTokenManagerInterface;
use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file.
 *
 * Do not forget to launch via bin/behat --snippets-for=FeatureContext
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
class FeatureContext implements Context
{
    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var RestContext|null
     */
    private $restContext;

    /**
     * FeatureContext constructor.
     *
     * @param KernelInterface $kernel the kernel to get services
     *
     * @throws RuntimeException when unable to create schema
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->manager = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->jwtManager = $kernel->getContainer()->get('lexik_jwt_authentication.jwt_manager');

        //We drop and load schema
        $schemaTool = new SchemaTool($this->manager);
        $schemaTool->dropDatabase();
        $metadata = $this->manager->getMetadataFactory()->getAllMetadata();
        try {
            $schemaTool->createSchema($metadata);
        } catch (ToolsException $e) {
            throw new RuntimeException('Unable to create schema.', 500, $e);
        }
    }

    /**
     * @Given /^database is clean$/
     */
    public function databaseIsClean(): void
    {
        $appFixtures = new AppFixtures();
        $loader = new Loader();
        $loader->addFixture($appFixtures);

        $purger = new ORMPurger();
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);

        $executor = new ORMExecutor($this->manager, $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * @Given /^I am logged as admin$/
     *
     * @throws RuntimeException when rest context is not set
     */
    public function iAmLoggedAsAdmin(): void
    {
        $this->iAmLoggedAs('admin');
    }

    /**
     * @Given /^I am logged as owner$/
     */
    public function iAmLoggedAsOwner(): void
    {
        $this->iAmLoggedAs('owner');
    }

    /**
     * @Given /^I am logged as user$/
     */
    public function iAmLoggedAsUser(): void
    {
        $this->iAmLoggedAs('user');
    }

    /**
     * @BeforeScenario @restContext
     *
     * @see https://symfony.com/doc/current/security/entity_provider.html#creating-your-first-user
     *
     * @param BeforeScenarioScope $scope the scope
     */
    public function restContext(BeforeScenarioScope $scope): void
    {
        /* @var RestContext $restContext the rest context */
        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
    }

    /**
     * @param string $username the username
     *
     * @throws RuntimeException when rest contest is not set
     */
    private function iAmLoggedAs(string $username): void
    {
        if (empty($this->restContext)) {
            throw new RuntimeException(
                'Rest context is not set. Did you forget to add @restContext before your scenario?'
            );
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->manager->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->findOneByEmail($username.'@example.org');
        $token = $this->jwtManager->create($user);
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer {$token}");
    }
}
