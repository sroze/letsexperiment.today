<?php

namespace App\Repository;

use App\Entity\Experiment;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineExperimentRepository implements ExperimentRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll()
    {
        return $this->entityManager->getRepository(Experiment::class)->findAll();
    }

    public function save(Experiment $experiment)
    {
        $this->entityManager->persist($experiment);
        $this->entityManager->flush();
    }

    public function find(string $uuid): ?Experiment
    {
        return $this->entityManager->getRepository(Experiment::class)->find($uuid);
    }
}
