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
    private $linkGenerator;
    private $urlGenerator;
    private $tokenStorage;
    private $renderAndSendEmails;

    public function __construct(LinkGenerator $linkGenerator, UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage, RenderAndSendEmails $renderAndSendEmails)
    {
        $this->linkGenerator = $linkGenerator;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->renderAndSendEmails = $renderAndSendEmails;
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
        $experimentUrl = $this->urlGenerator->generate('experiment', ['id' => $event->experiment->uuid], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->renderAndSendEmails->renderAndSend(
            $event->collaborator->email,
            sprintf('Collaborate with me on "%s"', $event->experiment->name),
            'experiment/emails/invited_has_collaborator.html.twig',
            [
                'link' => $this->linkGenerator->generateLink($event->collaborator->email, $experimentUrl),
                'experiment' => $event->experiment,
                'inviter' => $this->tokenStorage->getToken()->getUser()->getCollaborator(),
            ]
        );
    }
}
