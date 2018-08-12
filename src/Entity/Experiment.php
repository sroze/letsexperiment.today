<?php

namespace App\Entity;

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
}
