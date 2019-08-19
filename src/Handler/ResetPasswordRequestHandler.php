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

use App\Entity\ResetPasswordInterface;
use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Mailer\MailerInterface;
use App\Repository\UserRepository;
use App\Request\ResetPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Class ResetPasswordRequestHandler.
 */
final class ResetPasswordRequestHandler implements MessageHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * ResetPasswordRequestHandler constructor.
     *
     * @param EntityManagerInterface $entityManager entity manager to find and save user
     * @param MailerInterface        $mailer        mailer interface to send mail
     */
    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
        $this->mailer = $mailer;
    }

    /**
     * Invocation.
     *
     * @param ResetPasswordRequest $request the reset password request entity
     *
     * @throws UserNotFoundException when email correspond to no user
     * @throws Exception             when DateTime failed
     */
    public function __invoke(ResetPasswordRequest $request): void
    {
        //Find user or throw an exception.
        $user = $this->repository->findOneByEmail($request->getEmail());

        if (!$user instanceof ResetPasswordInterface) {
            throw new UserNotFoundException('Email not found');
        }

        $this->mailer->sendResettingEmailMessage($user);
        $user->requestReceived();
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
