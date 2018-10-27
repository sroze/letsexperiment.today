<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Collaborator", inversedBy="experiments")
     * @ORM\JoinTable(
     *     name="experiments_collaborators",
     *     joinColumns={@ORM\JoinColumn(name="experiment_uuid", referencedColumnName="uuid", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="collaborator_uuid", referencedColumnName="uuid")}
     * )
     *
     * @var Collaborator[]
     */
    public $collaborators;

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

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    public $createdAt;

    public function __construct()
    {
        $this->expectedOutcomes = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
        $this->checkIns = new ArrayCollection();
    }

    public function hasEnded()
    {
        return $this->isStarted() && $this->period->end < new \DateTime();
    }

    public function isStarted()
    {
        return $this->period !== null && $this->period->end !== null && $this->period->start !== null;
    }

    public function lastCheckIn() : ?CheckIn
    {
        $lastCriteria = Criteria::create()
            ->setMaxResults(1)
            ->orderBy(array('date' => Criteria::DESC));

        $lastCheckIn = $this->checkIns->matching($lastCriteria)->first();

        return $lastCheckIn !== false ? $lastCheckIn : null;
    }
}
