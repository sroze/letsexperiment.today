<?php

namespace App\Emails;

use App\Entity\Experiment;
use App\Events\AddedCollaborator;
use App\Events\Events;
use App\Events\ExperimentStarted;
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
            Events::START_EXPERIMENT => 'experimentStarted',
        ];
    }

    public function addedCollaborator(AddedCollaborator $event)
    {
        $this->renderAndSendEmails->renderAndSend(
            $event->collaborator->email,
            sprintf('Collaborate with me on "%s"', $event->experiment->name),
            'experiment/emails/invited_has_collaborator.html.twig',
            [
                'link' => $this->linkGenerator->generateLink($event->collaborator->email, $this->experimentUrl($event->experiment)),
                'experiment' => $event->experiment,
                'inviter' => $this->tokenStorage->getToken()->getUser()->getCollaborator(),
            ]
        );
    }

    public function experimentStarted(ExperimentStarted $event)
    {
        $experiment = $event->getExperiment();

        foreach ($experiment->collaborators as $collaborator) {
            $this->renderAndSendEmails->renderAndSend(
                $collaborator->email,
                sprintf('Experiment "%s" has started', $experiment->name),
                'experiment/emails/has_started.html.twig',
                [
                    'experiment' => $experiment,
                    'who_has_started' => $this->tokenStorage->getToken()->getUser()->getCollaborator(),
                ]
            );
        }
    }

    private function experimentUrl(Experiment $experiment)
    {
        return $this->urlGenerator->generate('experiment', ['id' => $experiment->uuid], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
