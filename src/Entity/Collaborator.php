<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"email"})
 */
class Collaborator
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
    public $email;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Experiment", mappedBy="collaborators")
     *
     * @var Experiment[]
     */
    public $experiments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CheckIn", mappedBy="collaborator")
     *
     * @var CheckIn[]
     */
    public $checkIns;

    public function __construct(string $email)
    {
        $this->email = $email;
    }
}
