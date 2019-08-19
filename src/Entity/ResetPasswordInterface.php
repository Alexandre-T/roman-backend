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

namespace App\Entity;

interface ResetPasswordInterface extends EmailInterface
{
    /**
     * New password was received, update data.
     *
     * @param string $password the new password
     */
    public function passwordReceived(string $password): void;

    /**
     * Request was received, update data.
     */
    public function requestReceived(): void;
}
