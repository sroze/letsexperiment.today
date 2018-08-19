<?php

namespace App\Repository;

use App\Entity\Experiment;
use App\Entity\SentEmailRecord;

interface SentEmailRecordRepository
{
    public function hasSent(Experiment $experiment, \DateTime $since, string $type) : bool;

    public function record(SentEmailRecord $reminder);
}
