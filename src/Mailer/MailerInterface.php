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

namespace App\Mailer;

use App\Entity\ActivationInterface;
use App\Entity\ResetPasswordInterface;

interface MailerInterface
{
    /**
     * Send an email to reset password.
     *
     * @param ResetPasswordInterface $user Mail recipient
     *
     * @return int
     */
    public function sendResettingEmailMessage(ResetPasswordInterface $user): int;

    /**
     * Send an email to user to share activation code.
     *
     * @param ActivationInterface $user the new user
     *
     * @return int
     */
    public function sendUserActivationMail(ActivationInterface $user): int;
}
