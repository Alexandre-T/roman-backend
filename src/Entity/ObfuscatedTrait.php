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

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

trait ObfuscatedTrait
{
    /**
     * UUID.
     *
     * @var string
     * @ORM\Column(type="uuid", nullable=false, unique=true)
     * @Groups({"book:read", "user:read"})
     */
    private $uuid;

    /**
     * ObfuscatedTrait constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->initUuid();
    }

    /**
     * Uuid getter.
     * If uuid is empty, generate a new one.
     *
     * @throws Exception
     *
     * @return string
     */
    public function getUuid(): string
    {
        if (null === $this->uuid) {
            self::initUuid();
        }

        return $this->uuid;
    }

    /**
     * Initialize UUID.
     *
     * @throws Exception
     */
    public function initUuid(): void
    {
        if (null === $this->uuid) {
            $this->uuid = Uuid::uuid4()->toString();
        }
    }
}
