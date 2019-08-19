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
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

trait ResetPasswordTrait
{
    /**
     * @var string the plain password
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(groups={"default"}, min="8", max="4096")
     * @Groups({"user:write"})
     * @ApiProperty
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $renewAt;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $renewCode;

    /**
     * @return DateTimeInterface|null
     */
    public function getRenewAt(): ?DateTimeInterface
    {
        return $this->renewAt;
    }

    /**
     * Renew code for password update getter.
     *
     * @return string|null
     */
    public function getRenewCode(): ?string
    {
        return $this->renewCode;
    }

    /**
     * New password was received, update data.
     *
     * @param string $password the new password
     */
    public function passwordReceived(string $password): void
    {
        $this->setPlainPassword($password);
        $this->setRenewAt(null);
        $this->setRenewCode(null);
    }

    /**
     * Request was received, update data.
     *
     * @throws Exception when Ramsey cannot generate a uuid4
     */
    public function requestReceived(): void
    {
        $this->setRenewAt(new DateTimeImmutable('now + 10 hours'));
        $this->setRenewCode(Uuid::uuid4()->toString());
    }

    /**
     * Set the non-persistent plain password.
     *
     * @param string $plainPassword non-encrypted password
     *
     * @return User
     */
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        // @see https://knpuniversity.com/screencast/symfony-security/user-plain-password
        $this->password = null;

        return $this;
    }

    /**
     * Renew expiration datetime.
     *
     * @param mixed $renewAt new expiration date time
     *
     * @return ResetPasswordTrait
     */
    public function setRenewAt($renewAt): self
    {
        $this->renewAt = $renewAt;

        return $this;
    }

    /**
     * Renew code fluent setter.
     *
     * @param string|null $renewCode A new code to provide to change password
     *
     * @return User
     */
    public function setRenewCode(?string $renewCode = null): self
    {
        $this->renewCode = $renewCode;

        return $this;
    }
}
