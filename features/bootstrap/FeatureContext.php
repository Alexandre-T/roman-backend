<?php

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
     * @var RestContext
     */
    private $restContext;

    /**
     * @var JWTTokenManagerInterface
     */
    private $jwtManager;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * FeatureContext constructor.
     *
     * @param KernelInterface $kernel the kernel to get services.
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->manager = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->jwtManager = $kernel->getContainer()->get('lexik_jwt_authentication.jwt_manager');
    }

    /**
     * @BeforeScenario @restContext
     *
     * @see https://symfony.com/doc/current/security/entity_provider.html#creating-your-first-user
     *
     * @param BeforeScenarioScope $scope the scope
     */
    public function restContext(BeforeScenarioScope $scope)
    {
        /** @var RestContext $restContext */
        $this->restContext = $scope->getEnvironment()->getContext(RestContext::class);
    }

    /**
     * @Given /^I am login as admin$/
     *
     * @throws RuntimeException when rest context is not set
     */
    public function iAmLoginAsAdmin()
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
        $this->restContext->iAddHeaderEqualTo('Authorization', "Bearer $token");
    }
}
