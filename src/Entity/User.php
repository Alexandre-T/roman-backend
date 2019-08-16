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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
 *         "post": {"access_control": "is_granted('create', object)"}
 *     },
 *     itemOperations={
 *         "get": {"access_control": "is_granted('show', object)"},
 *         "put": {"access_control": "is_granted('edit', object)"},
 *         "delete": {"access_control": "is_granted('delete', object)"},
 *     },
 *     denormalizationContext={"groups": {"user:write"}},
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
class User implements UserInterface, ObfuscatedInterface
{
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
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(max="180")
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
     * @Assert\NotBlank
     * @Assert\Length(min="5", max="255")
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
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="4096")
     * @Groups({"user:write"})
     * @ApiProperty()
     */
    private $plainPassword;

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
