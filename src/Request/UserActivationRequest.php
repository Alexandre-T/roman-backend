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

namespace App\Request;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     messenger=true,
 *     collectionOperations={
 *         "post": {
 *             "status": 202,
 *             "access_control": "is_anonymous()"
 *         }
 *     },
 *     itemOperations={},
 *     output=false
 * )
 *
 * FIXME Update the swagger documentation.
 */
final class UserActivationRequest
{
    /**
     * Activation code.
     *
     * @Assert\NotBlank
     * @Assert\Length(max="36")
     *
     * @var string
     */
    private $activation;

    /**
     * Activation code getter.
     *
     * @return string
     */
    public function getActivation(): string
    {
        return $this->activation;
    }

    /**
     * Activation code fluent setter.
     *
     * @param string $activation the new activation code
     *
     * @return UserActivationRequest
     */
    public function setActivation(string $activation): self
    {
        $this->activation = $activation;

        return $this;
    }
}
