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

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    public const ACTIONS = [self::ACTIVATE, self::CREATE, self::DELETE, self::EDIT, self::LIST, self::SHOW];
    public const ACTIVATE = 'activate';
    public const CREATE = 'create';
    public const DELETE = 'delete';
    public const EDIT = 'edit';
    public const LIST = 'list';
    public const SHOW = 'show';

    /**
     * Security layer.
     *
     * @var Security
     */
    private $security;

    /**
     * User voter constructor.
     *
     * @param Security $security security layer
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject): bool
    {
        if (!$subject instanceof User) {
            return false;
        }

        if (!in_array($attribute, self::ACTIONS, true)) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute attribute asked
     * @param mixed          $subject   entity forwarded
     * @param TokenInterface $token     authentication token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            switch ($attribute) {
                //Anonymous user can register
                //Anonymous user can activate their account
                case self::CREATE:
                case self::ACTIVATE:
                    return true;
            }

            return false;
        }

        //Authenticated user can only edit, delete, show himself
        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
            case self::SHOW:
                return $subject === $user;
            default:
                return false;
        }
    }
}
