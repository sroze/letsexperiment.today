<?php

namespace App\Repository;

use App\Entity\Experiment;
use App\Entity\SentEmailRecord;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineSentEmailRecordRepository implements SentEmailRecordRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function hasSent(Experiment $experiment, \DateTime $since, string $type): bool
    {
        $result = $this->entityManager->createQueryBuilder()
            ->select('count(r) as count')
            ->from(SentEmailRecord::class, 'r')
            ->where('r.experiment = :experiment AND r.sentAt > :since')
            ->andWhere('r.emailType = :type')
            ->setParameters([
                'experiment' => $experiment->uuid,
                'since' => $since,
                'type' => $type,
            ])
            ->getQuery()
            ->getOneOrNullResult();

        return $result['count'] > 0;
    }

    public function record(SentEmailRecord $reminder)
    {
        $this->entityManager->persist($reminder);
        $this->entityManager->flush();
    }
}
