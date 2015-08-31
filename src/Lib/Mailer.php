<?php

namespace Lib;

use Entities\Usuario;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use \Swift_Mailer;
use \Twig_Environment;

class Mailer
{
    const ROUTE_CONFIRM_EMAIL = 'confirm-email';
    const ROUTE_RESET_PASSWORD = 'reset-password';

    /** @var Swift_Mailer */
    protected $mailer;

    /** @var UrlGeneratorInterface  */
    protected $urlGenerator;

    /** @var Twig_Environment */
    protected $twig;

    public function __construct(Swift_Mailer $mailer, UrlGeneratorInterface $urlGenerator, Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function sendConfirmationMessage(Usuario $user)
    {
        $url = $this->urlGenerator->generate(self::ROUTE_CONFIRM_EMAIL, array('token' => $user->getConfirmationToken()), true);

        $context = array(
            'user' => $user,
            'confirmationUrl' => $url
        );

        $this->sendMessage($context, $user->getEmail(), 'confirmation');
    }

    public function sendResetMessage(Usuario $user)
    {
        $url = $this->urlGenerator->generate(self::ROUTE_RESET_PASSWORD, array('token' => $user->getConfirmationToken()), true);

        $context = array(
            'user' => $user,
            'resetUrl' => $url
        );

        $this->sendMessage($context, $user->getEmail(), 'reset-password');
    }


    /**
     * @param array  $context
     * @param string $email
     */
    protected function sendMessage($context, $email, $action)
    {
        $context = $this->twig->mergeGlobals($context);
        switch ($action) {
            case 'confirmation': $template = $this->twig->loadTemplate('emails/register-confirmation.twig');
                break;
            case 'reset-password': $template = $this->twig->loadTemplate('emails/reset-password.twig');
        }
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($GLOBALS['SENDER_EMAIL'])
            ->setTo($email);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }
}