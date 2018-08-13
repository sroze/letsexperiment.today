<?php

namespace App\SeamlessSecurity\Bridge;

use App\Entity\Collaborator;
use Symfony\Component\Security\Core\User\UserInterface;

class CollaboratorAsUser implements UserInterface
{
    private $collaborator;

    public function __construct(Collaborator $collaborator)
    {
        $this->collaborator = $collaborator;
    }

    /**
     * @return Collaborator
     */
    public function getCollaborator(): Collaborator
    {
        return $this->collaborator;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->collaborator->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}