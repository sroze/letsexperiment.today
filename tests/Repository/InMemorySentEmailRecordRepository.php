<?php

namespace App\Tests\Repository;

use App\Entity\Experiment;
use App\Entity\SentEmailRecord;
use App\Repository\SentEmailRecordRepository;

class InMemorySentEmailRecordRepository implements SentEmailRecordRepository
{
    private $sent = [];

    public function hasSent(Experiment $experiment, \DateTime $since, string $type): bool
    {
        return count(array_filter($this->sent, function(SentEmailRecord $reminder) use ($experiment, $since, $type) {
            return $reminder->experiment->uuid === $experiment->uuid && $reminder->sentAt > $since && $reminder->emailType == $type;
        })) > 0;
    }

    public function record(SentEmailRecord $reminder)
    {
        $this->sent[] = $reminder;
    }
}
