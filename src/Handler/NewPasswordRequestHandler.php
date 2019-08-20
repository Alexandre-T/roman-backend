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

use App\Entity\User;
use App\Exception\RenewCodeExpiredException;
use App\Exception\RenewCodeNotFoundException;
use App\Repository\UserRepository;
use App\Request\NewPasswordRequest;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * NewPassword request handler.
 */
final class NewPasswordRequestHandler implements MessageHandlerInterface
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
     * ResetPasswordRequestHandler constructor.
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
     * @param NewPasswordRequest $request the reset password request entity
     *
     * @throws RenewCodeNotFoundException when email correspond to no user
     * @throws RenewCodeExpiredException  when code is too old
     *
     * @TODO put all code in a service implementing an interface.
     */
    public function __invoke(NewPasswordRequest $request): void
    {
        //Find user or throw an exception.
        $user = $this->repository->findOneByRenewPasswordCode($request->getCode());

        if (!$user instanceof User) {
            throw new RenewCodeNotFoundException('This code is not valid to change password');
        }

        if ($user->getRenewAt() < new DateTimeImmutable()) {
            throw new RenewCodeExpiredException('Expired code.');
        }

        $user->passwordReceived($request->getPassword());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
