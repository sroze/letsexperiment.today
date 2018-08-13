<?php

namespace App\Events;

use App\Entity\Collaborator;
use App\Entity\Experiment;
use Symfony\Component\EventDispatcher\Event;

class AddedCollaborator extends Event
{
    public $experiment;
    public $collaborator;

    public function __construct(Experiment $experiment, Collaborator $collaborator)
    {
        $this->experiment = $experiment;
        $this->collaborator = $collaborator;
    }
}
