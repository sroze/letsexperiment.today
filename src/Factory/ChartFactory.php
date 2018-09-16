<?php


namespace App\Factory;


use App\Entity\Experiment;

class ChartFactory
{
    public function createExpectedOutcomeChartsForExperiment(Experiment $experiment): array
    {
        $charts = [];

        foreach ($experiment->expectedOutcomes as $expectedOutcome) {
            $points = [];

            // First point to chart is the starting value of the outcome
            $points[] = [
                $expectedOutcome->experiment->period->start->format('c'),
                floatval($expectedOutcome->currentValue)
            ];

            // Then add all the checked outcomes
            foreach($expectedOutcome->checkedOutcomes->toArray() as $checkedOutcome) {
                $points[] = [
                    $checkedOutcome->checkIn->date->format('c'),
                    floatval($checkedOutcome->currentValue)
                ];
            }

            // The last point is the target value
            $points[] = [
                $expectedOutcome->experiment->period->end->format('c'),
                floatval($expectedOutcome->expectedValue)
            ];

            $charts[$expectedOutcome->uuid] = [
                'name' => $expectedOutcome->name,
                'points' => $points
            ];
        }

        return $charts;
    }
}
