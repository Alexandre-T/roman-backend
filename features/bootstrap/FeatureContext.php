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

use App\Entity\User;
use App\Repository\UserRepository;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behatch\Context\RestContext;
use Doctrine\ORM\EntityManagerInterface as EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface as JWTTokenManagerInterface;
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
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->manager = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->jwtManager = $kernel->getContainer()->get('lexik_jwt_authentication.jwt_manager');
    }

    /**
     * @Given /^I am login as admin$/
     *
     * @throws RuntimeException when rest context is not set
     */
    public function iAmLoginAsAdmin(): void
    {
        if (empty($this->restContext)) {
            throw new RuntimeException(
                'Rest context is not set. Did you forget to add @restContext before your scenario?'
            );
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->manager->getRepository(User::class);
        /** @var User $user */
        $user = $userRepository->findOneByEmail('admin@example.org');
        $token = $this->jwtManager->create($user);
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer {$token}");
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
        /** @var RestContext $restContext */
        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
    }
}
