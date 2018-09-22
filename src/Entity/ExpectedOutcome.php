<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CheckedOutcome", mappedBy="expectedOutcome")
     * @var CheckedOutcome[]|Collection
     */
    public $checkedOutcomes;

    public function __construct()
    {
        $this->checkedOutcomes = new ArrayCollection();
    }

    public function isNumeric(): bool
    {
        return array_reduce(
            $this->checkedOutcomes->toArray(),
            function (bool $isNumeric, CheckedOutcome $checkedOutcome): bool
            {
                return $isNumeric && $checkedOutcome->isNumeric();
            },
            is_numeric($this->currentValue) && is_numeric($this->expectedValue)
        );
    }
}
