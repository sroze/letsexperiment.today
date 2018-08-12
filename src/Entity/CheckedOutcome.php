<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CheckedOutcome
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
     * @ORM\ManyToOne(targetEntity="App\Entity\CheckIn", inversedBy="checkedOutcomes")
     * @ORM\JoinColumn(name="check_in_uuid", referencedColumnName="uuid")
     *
     * @var Experiment
     */
    public $checkIn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ExpectedOutcome")
     * @ORM\JoinColumn(name="expected_outcome_uuid", referencedColumnName="uuid")
     *
     * @var Experiment
     */
    public $expectedOutcome;

    /**
     * @ORM\Column
     *
     * @var string
     */
    public $currentValue;
}
