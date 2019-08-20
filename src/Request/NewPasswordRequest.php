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
 * Renew password request.
 *
 * API received from front-end code sent by mail and the new password.
 *
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
final class NewPasswordRequest
{
    /**
     * The code sent by mail to user requesting a new mail.
     *
     * @var string
     *
     * @Assert\NotBlank
     */
    private $code;

    /**
     * The email to request a new password.
     *
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(min="5", max="180")
     *
     * @ApiProperty
     */
    private $password;

    /**
     * Code getter.
     *
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Email getter.
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Code fluent setter.
     *
     * @param string $code the code sent by mail
     *
     * @return NewPasswordRequest
     */
    public function setCode(string $code): NewPasswordRequest
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Email fluent setter.
     *
     * @param string $password the mail of user
     *
     * @return NewPasswordRequest
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
