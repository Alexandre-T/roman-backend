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

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

trait ActivationTrait
{
    /**
     * Entity activated.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Groups({"user:activated"})
     * @ApiProperty(readable=true)
     */
    private $activated = false;

    /**
     * Activation code.
     *
     * @ORM\Column(type="string", length=36)
     *
     * @var string
     */
    private $activationCode;

    /**
     * Activate the entity.
     */
    public function activate(): void
    {
        $this->activated = true;
    }

    /**
     * Inactivate the entity.
     */
    public function inactivate(): void
    {
        $this->activated = false;
    }

    /**
     * Initiate activation code.
     */
    public function initActivation(): void
    {
        $this->activationCode = md5(Uuid::uuid4()->toString());
    }

    /**
     * Is the current entity active?
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->activated;
    }

    /**
     * Verify the code.
     *
     * @param string $code code to activate entity
     *
     * @return bool
     */
    public function verify(string $code): bool
    {
        return $code === $this->activationCode;
    }
}
