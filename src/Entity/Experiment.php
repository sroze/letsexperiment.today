<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Experiment
{
    /**
     * @ORM\Id
     * @ORM\Column
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    public $uuid;

    /**
     * @ORM\Column
     *
     * @var string
     */
    public $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ExpectedOutcome", mappedBy="experiment")
     *
     * @var ExpectedOutcome[]
     */
    public $expectedOutcomes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CheckIn", mappedBy="experiment")
     *
     * @var CheckIn[]
     */
    public $checkIns;

    /**
     * @ORM\Embedded(class="App\Entity\ExperimentPeriod")
     *
     * @var ExperimentPeriod
     */
    public $period;

    public function __construct()
    {
        $this->expectedOutcomes = new ArrayCollection();
    }

    public function isStarted()
    {
        return $this->period->end !== null && $this->period->start !== null;
    }
}
