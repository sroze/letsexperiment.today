<?php

namespace App\Events;

use App\Entity\Experiment;
use Symfony\Component\EventDispatcher\Event;

class ExperimentStarted extends Event
{
    private $experiment;

    public function __construct(Experiment $experiment)
    {
        $this->experiment = $experiment;
    }

    public function getExperiment(): Experiment
    {
        return $this->experiment;
    }
}
