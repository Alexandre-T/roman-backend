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

namespace App\Handler;

use App\Entity\ActivationInterface;
use App\Entity\User;
use App\Exception\BadActivationCodeException;
use App\Exception\UserAlreadyActiveException;
use App\Repository\UserRepository;
use App\Request\UserActivationRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class UserActivationRequestHandler.
 */
final class UserActivationRequestHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * UserActivationRequestHandler constructor.
     *
     * @param EntityManagerInterface $entityManager entity manager to find and save user
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }

    /**
     * Invocation.
     *
     * @param UserActivationRequest $request the reset password request entity
     *
     * @throws BadActivationCodeException when activation code correspond to no user
     * @throws UserAlreadyActiveException when user is already active
     */
    public function __invoke(UserActivationRequest $request): void
    {
        //Find user or throw an exception.
        $user = $this->repository->findOneByActivationCode($request->getActivation());

        if (!$user instanceof ActivationInterface) {
            throw new BadActivationCodeException('Activation code is invalid');
        }

        if ($user->isActive()) {
            throw new UserAlreadyActiveException('User already active');
        }

        if (!$user->verify($request->getActivation())) {
            throw new BadActivationCodeException('Activation code is invalid');
        }

        $user->activate();
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
