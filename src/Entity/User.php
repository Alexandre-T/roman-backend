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
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UserActivation;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User entity.
 *
 * @ApiResource(
 *     collectionOperations={
 *         "get": {"access_control": "is_granted('ROLE_ADMIN')"},
 *         "post": {"access_control": "is_granted('create', object)", "validation_groups": {"default"}}
 *     },
 *     itemOperations={
 *         "get": {"access_control": "is_granted('show', object)"},
 *         "put": {"access_control": "is_granted('edit', object)", "validation_groups": {"default"}},
 *         "delete": {"access_control": "is_granted('delete', object)"},
 *         "activate": {
 *             "access_control": "is_granted('activate', object)",
 *             "attributes": {"validation_groups": {"activation"}},
 *             "controller": UserActivation::class,
 *             "denormalization_context": {"groups": {"user:activate"}},
 *             "method": "PUT",
 *             "normalization_context": {"groups": {"user:activated"}},
 *             "path": "/users/{id}/activate"
 *         }
 *     },
 *     denormalizationContext={"groups": {"user:write", "user:renew"}},
 *     normalizationContext={"groups": {"user:read"}},
 *     iri="https://schema.org/Person"
 * )
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="ts_user", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="uk_user_mail", columns={"email"}),
 *     @ORM\UniqueConstraint(name="uk_user_nickname", columns={"nickname"}),
 *     @ORM\UniqueConstraint(name="uk_user_uuid", columns={"uuid"})
 * })
 *
 * @UniqueEntity(fields={"nickname"})
 * @UniqueEntity(fields={"email"})
 */
class User implements ActivationInterface, UserInterface, ObfuscatedInterface
{
    //To implement Activation interface.
    use ActivationTrait;
    //To implement obfuscated interface.
    use ObfuscatedTrait;

    /**
     * @Groups({"user:read"})
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="owner", orphanRemoval=true)
     */
    private $books;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write"})
     *
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Email(groups={"default"})
     * @Assert\Length(max="180", groups={"default"})
     *
     * @ApiProperty(iri="https://schema.org/email")
     */
    private $email;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user:read", "user:write", "book:item:get"})
     *
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(min="5", max="255", groups={"default"})
     *
     * @ApiProperty(iri="https://schema.org/name")
     */
    private $nickname;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string the plain password
     * @Assert\NotBlank(groups={"default"})
     * @Assert\Length(groups={"default"}, min="8", max="4096")
     * @Groups({"user:write"})
     * @ApiProperty
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $renewAt;

    /**
     * @ORM\Column(type="string", length=36, nullable=true)
     */
    private $renewCode;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:read"})
     */
    private $roles = ['ROLE_USER'];

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->initUuid();
        $this->initActivation();
        $this->books = new ArrayCollection();
    }

    /**
     * Book fluent adder.
     *
     * @param Book $book Book to add
     *
     * @return User
     */
    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setOwner($this);
        }

        return $this;
    }

    /**
     * Erase sensitive data.
     *
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * Book getter.
     *
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * Email getter.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Identifier getter.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Nickname getter.
     *
     * Nickname is used, because Username is used by UserInterface to return email.
     *
     * @return string
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * Password getter.
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * Plain password getter.
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

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
     * Roles getter.
     *
     * @see UserInterface
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Salt Getter.
     *
     * @see UserInterface
     */
    public function getSalt(): void
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * Is user activated?
     *
     * @return bool
     */
    public function isActivated(): bool
    {
        return $this->activated;
    }

    /**
     * Book fluent remover.
     * Be careful, book will be deleted.
     *
     * @param Book $book book to delete
     *
     * @return User
     */
    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getOwner() === $this) {
                $book->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * Email fluent setter.
     *
     * @param string $email the new email
     *
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Username fluent setter.
     *
     * @param string $nickname the new username
     *
     * @return User
     */
    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Password fluent setter.
     *
     * @param string $password the new password
     *
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the non-persistent plain password.
     *
     * @param string $plainPassword non-encrypted password
     *
     * @return User
     */
    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        // @see https://knpuniversity.com/screencast/symfony-security/user-plain-password
        $this->password = null;

        return $this;
    }

    /**
     * Renew code fluent setter.
     *
     * @param string|null $renewCode A new code to provide to change password
     *
     * @throws Exception when DateTimeImmutable cannot be created
     *
     * @return User
     */
    public function setRenewCode(?string $renewCode = null): self
    {
        $this->renewCode = $renewCode;

        if (null === $renewCode) {
            $this->renewAt = null;
        } else {
            $this->renewAt = new DateTimeImmutable();
        }

        return $this;
    }

    /**
     * Roles fluent setter.
     *
     * @param array $roles an array of roles like ROLE_ADMIN, ROLE_USER
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}
