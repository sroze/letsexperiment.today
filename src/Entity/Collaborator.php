<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var ArrayCollection|Experiment[]
     */
    public $experiments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CheckIn", mappedBy="collaborator")
     *
     * @var ArrayCollection|CheckIn[]
     */
    public $checkIns;

    public function __construct(string $email)
    {
        $this->email = $email;
        $this->experiments = new ArrayCollection();
        $this->checkIns = new ArrayCollection();
    }
}
