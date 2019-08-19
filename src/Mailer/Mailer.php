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

namespace App\Mailer;

use App\Entity\ActivationInterface;
use App\Entity\ResetPasswordInterface;
use Swift_Mailer;
use Twig;

class Mailer implements MailerInterface
{
    /**
     * Swift mailer.
     *
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * Twig engine.
     *
     * @var Twig\Environment
     */
    protected $templating;

    /**
     * Parameters.
     *
     * @var array sender, etc
     */
    private $parameters;

    /**
     * Mailer constructor.
     *
     * @param Swift_Mailer     $mailer     mailer
     * @param Twig\Environment $templating the templating engine
     * @param array            $parameters the env parameters (expediter)
     */
    public function __construct(
     Swift_Mailer $mailer,
     Twig\Environment $templating,
     array $parameters
    ) {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->parameters = $parameters['parameters'];
    }

    /**
     * Send a mail to reset password.
     *
     * @param ResetPasswordInterface $user recipient mail
     *
     * @throws Twig\Error\LoaderError  on loader error
     * @throws Twig\Error\RuntimeError on runtime error
     * @throws Twig\Error\SyntaxError  on syntax error
     *
     * @return int
     */
    public function sendResettingEmailMessage(ResetPasswordInterface $user): int
    {
        $url = $this->parameters['changePasswordUrl'];
        $templateHtml = $this->templating->load('mail/resetting.html.twig');
        $renderHtml = $templateHtml->render([
            'user' => $user,
            'confirmationUrl' => $url,
        ]);

        $templateTxt = $this->templating->load('mail/resetting.txt.twig');
        $renderTxt = $templateTxt->render([
            'user' => $user,
            'confirmationUrl' => $url,
        ]);

        return $this->sendEmailMessage($renderHtml, $renderTxt, $this->parameters['from'], $user->getEmail());
    }

    /**
     * Send an email to user to share activation code.
     *
     * @param ActivationInterface $user the new user
     *
     * @throws Twig\Error\LoaderError  on loader error
     * @throws Twig\Error\RuntimeError on runtime error
     * @throws Twig\Error\SyntaxError  on syntax error
     *
     * @return int
     */
    public function sendUserActivationMail(ActivationInterface $user): int
    {
        $url = $this->parameters['activationUrl'];
        $templateHtml = $this->templating->load('mail/activating.html.twig');
        $renderHtml = $templateHtml->render([
            'user' => $user,
            'confirmationUrl' => $url,
        ]);
        $templateTxt = $this->templating->load('mail/activating.html.twig');
        $renderTxt = $templateTxt->render([
            'user' => $user,
            'confirmationUrl' => $url,
        ]);

        return $this->sendEmailMessage($renderHtml, $renderTxt, $this->parameters['from'], $user->getEmail());
    }

    /**
     * Send a mail.
     *
     * @param string       $html      the mail body in html
     * @param string       $txt       the mail body in txt
     * @param string       $fromEmail mail expediter
     * @param array|string $toEmail   mail recipient
     *
     * @return int the number of sent mail
     */
    protected function sendEmailMessage(string $html, string $txt, string $fromEmail, $toEmail): int
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($txt));
        $subject = array_shift($renderedLines);
        $txt = implode("\n", $renderedLines);
        $message = ($this->mailer->createMessage())
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($html, 'text/html')
            ->addPart($txt, 'text/plain')
        ;

        return $this->mailer->send($message);
    }
}
