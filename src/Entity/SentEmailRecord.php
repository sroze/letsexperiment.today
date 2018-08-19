<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class SentEmailRecord
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Experiment")
     * @ORM\JoinColumn(name="experiment_uuid", referencedColumnName="uuid")
     *
     * @var Experiment
     */
    public $experiment;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    public $sentAt;

    /**
     * @ORM\Column
     *
     * @var string
     */
    public $recipientEmail;

    /**
     * @ORM\Column
     *
     * @var string
     */
    public $emailType;

    public function __construct(Experiment $experiment, string $emailType, string $recipientEmail, $sentAt = null)
    {
        $this->experiment = $experiment;
        $this->emailType = $emailType;
        $this->recipientEmail = $recipientEmail;
        $this->sentAt = $sentAt ?: new \DateTime();
    }
}
