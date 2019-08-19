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

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     messenger=true,
 *     collectionOperations={
 *         "post": {"status": 202},
 *     },
 *     itemOperations={},
 *     output=false
 * )
 *
 * FIXME Update the swagger documentation.
 */
final class ResetPasswordRequest
{
    /**
     * The email to request a new password.
     *
     * @var string
     *
     * @Assert\Email
     * @Assert\NotBlank
     * @Assert\Length(max="180")
     *
     * @ApiProperty
     */
    private $email;

    /**
     * Email getter.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Email fluent setter.
     *
     * @param string $email the mail of user
     *
     * @return ResetPasswordRequest
     */
    public function setEmail(string $email): ResetPasswordRequest
    {
        $this->email = $email;

        return $this;
    }
}
