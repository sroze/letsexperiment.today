<?php

namespace App\SeamlessSecurity\Bridge;

use App\Entity\Collaborator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CollaboratorProvider implements UserProviderInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $collaborator = $this->entityManager->getRepository(Collaborator::class)->findOneBy([
            'email' => $username,
        ]);

        if (null === $collaborator) {
            $collaborator = new Collaborator();
            $collaborator->email = $username;

            $this->entityManager->persist($collaborator);
            $this->entityManager->flush();
        }

        return new CollaboratorAsUser($collaborator);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === CollaboratorAsUser::class;
    }
}
