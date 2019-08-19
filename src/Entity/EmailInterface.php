<?php


namespace App\Entity;

/**
 * Email Interface
 */
interface EmailInterface
{
    /**
     * Email getter.
     *
     * @return EmailInterface
     */
    public function getEmail(): ?string;
}