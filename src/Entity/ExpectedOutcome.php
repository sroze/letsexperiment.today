<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ExpectedOutcome
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Experiment", inversedBy="expectedOutcomes")
     * @ORM\JoinColumn(name="experiment_uuid", referencedColumnName="uuid", onDelete="CASCADE")
     *
     * @var Experiment
     */
    public $experiment;

    /**
     * @ORM\Column
     *
     * @var string
     */
    public $name;

    /**
     * @ORM\Column
     *
     * @var string
     */
    public $currentValue;

    /**
     * @ORM\Column
     *
     * @var string
     */
    public $expectedValue;
}
