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

use App\Entity\User;

interface MailerInterface
{
    /**
     * Send an email to reset password.
     *
     * @param User $user Mail recipient
     *
     * @return int
     */
    public function sendResettingEmailMessage(User $user): int;

    /**
     * Send an email to user to share activation code.
     *
     * @param User $user the new user
     *
     * @return int
     */
    public function sendUserActivationMail(User $user): int;
}
