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

namespace App\EventSuscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * User mail subscriber.
 *
 * Send a mail each time a user is created
 */
final class UserMailSubscriber implements EventSubscriberInterface
{
    private $mailer;

    /**
     * UserMailSubscriber constructor.
     *
     * @param MailerInterface $mailer the mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Subscribes on POST_WRITE event.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['sendMail', EventPriorities::POST_WRITE],
        ];
    }

    /**
     * Send a mail to rovide activation code.
     *
     * @param ViewEvent $event the view event
     */
    public function sendMail(ViewEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$user instanceof User || Request::METHOD_POST !== $method) {
            return;
        }

        $this->mailer->sendUserActivationMail($user);
    }
}
