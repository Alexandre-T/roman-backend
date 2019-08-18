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

namespace App\Controller;

use ApiPlatform\Core\Exception\PropertyNotFoundException;
use App\Entity\ActivationInterface;
use App\Exception\BadActivationCodeException;
use App\Exception\UserAlreadyActiveException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserActivation extends AbstractController
{
    /**
     * Search the activation code into request content.
     *
     * @throws PropertyNotFoundException when activation is not provided
     *
     * @return string
     */
    private function getActivationCode(): string
    {
        //try to handle activation code via body.
        $requestStack = $this->get('request_stack');
        $content = $requestStack->getCurrentRequest()->getContent();
        $contentJson = json_decode($content, false, 2);
        if (null === $contentJson) {
            throw new PropertyNotFoundException('Activation code is required');
        }

        if (empty($contentJson->activation)) {
            throw new PropertyNotFoundException('Activation code cannot be blank');
        }

        return $contentJson->activation;
    }

    /**
     * @param ActivationInterface $data user retrieve by API Platform ADR pattern
     *
     * @throws UserAlreadyActiveException when user is already active
     * @throws BadActivationCodeException when activation forwarded is not corresponding to the one in database
     *
     * @return ActivationInterface
     */
    public function __invoke(ActivationInterface $data)
    {
        if ($data->isActive()) {
            throw new UserAlreadyActiveException('User already active');
        }

        try {
            if ($data->verify($this->getActivationCode())) {
                $data->activate();
            } else {
                throw new BadActivationCodeException('Bad activation code');
            }
        } catch (PropertyNotFoundException $e) {
            throw new BadActivationCodeException('Activation code should not be blank');
        }

        return $data;
    }
}
