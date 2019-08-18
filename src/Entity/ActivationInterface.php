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

/**
 * Activation interface.
 */
interface ActivationInterface
{
    /**
     * Activate the entity.
     */
    public function activate(): void;

    /**
     * Inactivate the entity.
     */
    public function inactivate(): void;

    /**
     * Is the entity active?
     */
    public function isActive(): bool;

    /**
     * Verify the code.
     *
     * @param string $code code to activate interface
     *
     * @return bool
     */
    public function verify(string $code): bool;
}
