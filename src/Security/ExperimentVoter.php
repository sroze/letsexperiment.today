<?php

namespace App\Security;

use App\Entity\Collaborator;
use App\Entity\Experiment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExperimentVoter extends Voter
{
    const EDIT = 'EDIT';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return self::EDIT === $attribute && $subject instanceof Experiment;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if ($attribute !== self::EDIT) {
            return false;
        }

        return $subject->collaborators->exists(function($key, Collaborator $collaborator) use ($token) {
            return $collaborator->email === $token->getUsername();
        });
    }
}
