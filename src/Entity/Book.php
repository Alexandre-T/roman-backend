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

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Book entity.
 *
 * @ApiResource(
 *     collectionOperations={"get", "post"},
 *     denormalizationContext={"groups": {"book:write"}},
 *     itemOperations={"get", "put", "delete"},
 *     normalizationContext={"groups": {"book:read", "book:item:get"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\Table(name="te_book")
 */
class Book implements ObfuscatedInterface
{
    //To implement obfuscated interface.
    use ObfuscatedTrait;

    /**
     * @Groups({"book:read", "book:write", "user:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $author;

    /**
     * @Groups({"book:read", "book:write"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $biography;

    /**
     * @Groups({"book:read", "book:write", "user:read"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $dramaPitch;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"book:read", "book:write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @Groups({"book:read", "book:write"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $taglinePitch;

    /**
     * @Groups({"book:read", "book:write", "user:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Groups({"book:read", "book:write"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $trajectorialPitch;

    /**
     * Author getter.
     *
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Biography getter.
     *
     * @return string|null
     */
    public function getBiography(): ?string
    {
        return $this->biography;
    }

    /**
     * Drama pitch getter.
     *
     * @return string|null
     */
    public function getDramaPitch(): ?string
    {
        return $this->dramaPitch;
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
     * Owner getter.
     *
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * Tagline pitch getter.
     *
     * @return string|null
     */
    public function getTaglinePitch(): ?string
    {
        return $this->taglinePitch;
    }

    /**
     * Title getter.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Trajectorial pitch getter.
     *
     * @return string|null
     */
    public function getTrajectorialPitch(): ?string
    {
        return $this->trajectorialPitch;
    }

    /**
     * Author fluent setter.
     *
     * @param string|null $author the author
     *
     * @return Book
     */
    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Biography fluent setter.
     *
     * @param string|null $biography the new biography of author
     *
     * @return Book
     */
    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Drama pitch fluent setter.
     *
     * @param string|null $dramaPitch the drama pitch
     *
     * @return Book
     */
    public function setDramaPitch(?string $dramaPitch): self
    {
        $this->dramaPitch = $dramaPitch;

        return $this;
    }

    /**
     * Owner fluent setter.
     *
     * @param User|null $owner owner of book
     *
     * @return Book
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Tagline pitch fluent setter.
     *
     * @param string|null $taglinePitch the tagline pitch
     *
     * @return Book
     */
    public function setTaglinePitch(?string $taglinePitch): self
    {
        $this->taglinePitch = $taglinePitch;

        return $this;
    }

    /**
     * Title fluent setter.
     *
     * @param string $title the title
     *
     * @return Book
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Trajectorial pitch fluent setter.
     *
     * @param string|null $trajectorialPitch the trajectorial pitch
     *
     * @return Book
     */
    public function setTrajectorialPitch(?string $trajectorialPitch): self
    {
        $this->trajectorialPitch = $trajectorialPitch;

        return $this;
    }
}
