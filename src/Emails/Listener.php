<?php

namespace App\Emails;

use App\Events\AddedCollaborator;
use App\Events\Events;
use App\SeamlessSecurity\Link\LinkGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Listener implements EventSubscriberInterface
{
    private $mailer;
    private $twig;
    private $linkGenerator;
    private $urlGenerator;
    private $tokenStorage;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, LinkGenerator $linkGenerator, UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->linkGenerator = $linkGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::ADD_COLLABORATOR => 'addedCollaborator',
        ];
    }

    public function addedCollaborator(AddedCollaborator $event)
    {
        $experimentUrl = $this->urlGenerator->generate('experiment', ['id' => $event->experiment->uuid]);

        try {
            $contents = $this->twig->render(
                'experiment/emails/invited_has_collaborator.html.twig',
                [
                    'link' => $this->linkGenerator->generateLink($event->collaborator->email, $experimentUrl),
                    'experiment' => $event->experiment,
                    'inviter' => $this->tokenStorage->getToken()->getUser()->getCollaborator(),
                ]
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not send email', $e->getCode(), $e);
        }

        $message = (new \Swift_Message(sprintf('Collaborate on "%s"', $event->experiment->name)))
            ->setFrom('samuel@letsexperiment.today', 'Samuel from LetsExperiment.today')
            ->setTo($event->collaborator->email)
            ->setBody($contents,'text/html');

        $this->mailer->send($message);
    }
}
