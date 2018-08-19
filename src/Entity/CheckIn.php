<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CheckIn
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Experiment", inversedBy="checkIns")
     * @ORM\JoinColumn(name="experiment_uuid", referencedColumnName="uuid")
     *
     * @var Experiment
     */
    public $experiment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Collaborator", inversedBy="checkIns")
     * @ORM\JoinColumn(name="collaborator_uuid", referencedColumnName="uuid")
     *
     * @var Experiment
     */
    public $collaborator;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    public $date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CheckedOutcome", mappedBy="checkIn")
     *
     * @var CheckedOutcome[]
     */
    public $checkedOutcomes;

    public function __construct()
    {
        $this->checkedOutcomes = new ArrayCollection();
    }

    public function checkedOutcomeForExpectedOutcome(ExpectedOutcome $expectedOutcome) : ?CheckedOutcome
    {
        foreach ($this->checkedOutcomes as $outcome) {
            if ($outcome->expectedOutcome->uuid === $expectedOutcome->uuid) {
                return $outcome;
            }
        }

        return null;
    }
}
