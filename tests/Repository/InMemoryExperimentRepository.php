<?php

namespace App\Tests\Repository;

use App\Entity\Experiment;
use App\Repository\ExperimentRepository;

class InMemoryExperimentRepository implements ExperimentRepository
{
    private $experiments = [];

    public function findAll()
    {
        return array_values($this->experiments);
    }

    public function save(Experiment $experiment)
    {
        $this->experiments[$experiment->uuid] = $experiment;
    }

    public function find(string $uuid): ?Experiment
    {
        return $this->experiments[$uuid] ?? null;
    }
}
