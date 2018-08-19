<?php

namespace App\Repository;

use App\Entity\Experiment;

interface ExperimentRepository
{
    /**
     * @return Experiment[]
     */
    public function findAll();

    public function find(string $uuid) : ?Experiment;

    public function save(Experiment $experiment);
}
